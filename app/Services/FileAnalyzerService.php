<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use PhpOffice\PhpWord\Element\Image;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\IOFactory;
use Smalot\PdfParser\Page;
use Smalot\PdfParser\Parser;

class FileAnalyzerService
{
    private const WORDS_PER_PAGE = 550;

    private function analyzeImage(string $fullPath): array
    {
        $results = [
            'total_images' => 1,
            'total_pixels' => 0,
            'total_pages' => 1,
            'colored_pages' => 0,
            'black_white_pages' => 0,
        ];

        $manager = new ImageManager(new Driver());
        $image = $manager->read($fullPath);

        $results['total_pixels'] = $this->countPixels($image);
        $this->isImageColored($image) ? $results['colored_pages']++ : $results['black_white_pages']++;

        return $results;
    }

    private function countPixels(ImageInterface $image): int
    {
        return $image->width() * $image->height();
    }

    private function isImageColored(ImageInterface $image): bool
    {
        $width = $image->width();
        $height = $image->height();
        if ($width < 10 || $height < 10) return false;

        for ($y = 0; $y < $height; $y += 10) {
            for ($x = 0; $x < $width; $x += 10) {
                $color = $image->pickColor($x, $y);
                if (!$this->isGreyscale($color->toHex())) return true;
            }
        }
        return false;
    }

    private function isGreyscale(string $hexColor): bool
    {
        $hex = str_split($hexColor, 2);
        return ($hex[0] === $hex[1]) && ($hex[1] === $hex[2]);
    }

    private function analyzeDOCX(string $fullPath): array
    {
        $results = [
            'total_pages' => 0,
            'total_images' => 0,
            'colored_pages' => 0,
            'black_white_pages' => 0,
            'total_pixels' => 0,
        ];

        $phpWord = IOFactory::load($fullPath);
        $coloredWords = 0;
        $blackWords = 0;

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                    foreach ($element->getElements() as $textElement) {
                        if ($textElement instanceof \PhpOffice\PhpWord\Element\Text) {
                            $text = $textElement->getText();
                            $fontStyle = $textElement->getFontStyle();
                            $wordCount = str_word_count($text);
                            if ($fontStyle && $fontStyle->getColor()) {
                                $coloredWords += $wordCount;
                            } else {
                                $blackWords += $wordCount;
                            }
                        }
                    }
                } elseif ($element instanceof Image) {
                    $results['total_images']++;
                    // Implement color analysis here by extracting the color
                    // Assuming it counts colored words/images for this context
                    $coloredWords++;
                } elseif ($element instanceof Table) {
                    // Assuming tables are black and white
                    $blackWords++;
                }
            }
        }

        $results['black_white_pages'] = ceil($blackWords / self::WORDS_PER_PAGE);
        $results['colored_pages'] = ceil($coloredWords / self::WORDS_PER_PAGE);
        $results['total_pages'] = max(1, $results['black_white_pages'] + $results['colored_pages']);

        return $results;
    }

    private function determineImageFormat(?string $filter): string
    {
        return match (strtolower($filter)) {
            'jpxdecode', 'jp2000decode' => 'jp2',
            'flatedecode' => 'png',
            default => 'jpg',
        };
    }

    private function analyzePDF(string $filePath): array
    {
        $results = [
            'total_pages' => 0,
            'total_images' => 0,
            'colored_pages' => 0,
            'black_white_pages' => 0,
            'total_pixels' => 0,
        ];

        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($filePath);
            $pages = $pdf->getPages();
            $results['total_pages'] = count($pages);
            $directoryPath = 'uploads/pdf/extracted_images/';

            if (!Storage::disk('public')->exists($directoryPath)) {
                Storage::disk('public')->makeDirectory($directoryPath);
            }

            foreach ($pages as $page) {
                $this->processPDFPage($page,$results, $directoryPath);

                $text = $page->getText();
                if (preg_match('/#[0-9A-Fa-f]{6}|#[0-9A-Fa-f]{3}/', $text)) {
                    $results['colored_pages']++;
                } else {
                    $results['black_white_pages']++;
                }
            }
        } catch (Exception $e) {
            Log::error('Error analyzing PDF: ' . $e->getMessage());
        }

        return $results;
    }

    private function processPDFPage(Page $page, array &$results, string $directoryPath): void
    {
        $resources = $page->getXObjects();
//        if (!isset($resources['XObject'])) return;
        foreach ($resources['XObject'] as $object) {
            print_r($object);
            if (isset($object['Subtype']) && strtolower($object['Subtype']) === 'image') {
                $results['total_images']++;
                $imageData = $object['Data'];
                $imageFormat = $this->determineImageFormat($object['Filter']);
                $imageName = 'image_' . uniqid() . '.' . $imageFormat;
                $imagePath = $directoryPath . $imageName;
                Storage::disk('public')->put($imagePath, $imageData);
                $this->analyzePDFImage($imagePath, $results);
                Storage::disk('public')->delete($imagePath); // Clear image after processing
            }
        }
    }

    private function analyzePDFImage(string $imagePath, array &$results): void
    {
        $result = $this->analyzeImage($imagePath);
        $results['total_images'] += $result['total_images'];
        $results['colored_pages'] += $result['colored_pages'];
        $results['black_white_pages'] += $result['black_white_pages'];
        $results['total_pixels'] += $result['total_pixels'];
    }

    /**
     * @throws Exception
     */
    public function analyze(string $filePath): array
    {
        $fullPath = Storage::disk('public')->path($filePath);
        if (!file_exists($fullPath)) throw new Exception("File does not exist at: " . $fullPath);

        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        return match ($extension) {
            'pdf' => $this->analyzePDF($fullPath),
            'docx' => $this->analyzeDOCX($fullPath),
            'jpeg', 'jpg', 'png' => $this->analyzeImage($fullPath),
            default => throw new Exception("Unsupported file type: ." . $extension),
        };
    }
}
