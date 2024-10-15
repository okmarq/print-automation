<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use PhpOffice\PhpWord\Element\Image;
use PhpOffice\PhpWord\Element\Link;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Title;
use PhpOffice\PhpWord\IOFactory;
use Smalot\PdfParser\Parser;

class FileAnalyzerService
{
    private const WORDS_PER_PAGE = 550;
    private const PARAGRAPHS_PER_PAGE = 10;

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
        $tempDir = 'uploads/docx/images/';

        if (!Storage::disk('public')->exists($tempDir)) {
            Storage::disk('public')->makeDirectory($tempDir);
        }

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if ($element instanceof TextRun) {
                    foreach ($element->getElements() as $textElement) {
                        if ($textElement instanceof Text || $textElement instanceof Link) {
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
                } elseif ($element instanceof Title) {
                    if ($element->getText() instanceof TextRun) {
                        $text = $element->getText()->getText();
                    } else {
                        $text = $element->getText();
                    }
                    $wordCount = str_word_count($text);
                    $blackWords += $wordCount;
                    $blackWords++;
                } elseif ($element instanceof Table) {
                    if ($element->getRows() != null) {
                        $blackWords += count($element->getRows()) * self::PARAGRAPHS_PER_PAGE;
                    }
                } elseif ($element instanceof Image) {
                    // it would seem that the PhpOffice package is unable to find images in a docx file so this section can't be reached.
                    // However, what this section does is to get the extracted image and pass it into the analyzeImage function to bring back  a
                    // colored or black and white count with the page number incremented.
                    // A more accurate approach will be to use the size of the image in correlation with the standard A4 size and deduce the page
                    // consumption.
                    $imageData = $element->getImageStringData();
                    $imagePath = $tempDir . uniqid() . '.jpg';
                    Storage::disk('public')->put($imagePath, $imageData);
                    $result = $this->analyzeImage(Storage::get($imagePath));
                    $results['total_pages'] += $result['total_pages'];
                    $results['total_images'] += $result['total_images'];
                    $results['colored_pages'] += $result['colored_pages'];
                    $results['black_white_pages'] += $result['black_white_pages'];
                    $results['total_pixels'] += $result['total_pixels'];
                    $results['colored_pages'] ? $coloredWords++ : $blackWords++;
                    Storage::disk('public')->delete($imagePath);
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

    /**
     * @throws Exception
     */
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
            $tempDir = 'uploads/pdf/images/';
            if (!Storage::disk('public')->exists($tempDir)) {
                Storage::disk('public')->makeDirectory($tempDir);
            }
            // i'll use the colored images to determine if the pdf is colored due to the inability of the package to determine if the text in the
            // pdf is colored
            $coloredPages = 0;
            foreach ($pages as $page) {
                $resources = $page->getXObjects();
                foreach ($resources as $object) {
                    if ($object->get('Subtype') != null && strtolower($object->get('Subtype')) === 'image') {
                        $imageFormat = $this->determineImageFormat($object->get('Filter'));
                        $imageName = 'image_' . uniqid() . '.' . $imageFormat;
                        $imagePath = $tempDir . $imageName;
                        Storage::disk('public')->put($imagePath, $object->getContent());
                        $result = $this->analyzeImage(Storage::get($imagePath));
                        $results['total_images'] += $result['total_images'];
                        $results['total_pixels'] += $result['total_pixels'];
                        // since pdf has a structures page calculation, we rely on the colored effect of the pages to tell us if the page is colored or not
                        // instead of using the image that makes up the page
//                $results['total_pages'] += $result['total_pages'];
//                $results['colored_pages'] += $result['colored_pages'];
//                $results['black_white_pages'] += $result['black_white_pages'];
                        if ($result['colored_pages'] > $coloredPages) {
                            $coloredPages++;
                        }
                        Storage::disk('public')->delete($imagePath);
                    }
                }
                // increment only the black pages due to the insufficiency
                $results['black_white_pages']++;
            }
            // use the image to determine the colored paged
            $results['colored_pages'] = $coloredPages;
            // then decrement the black pages by the colored page numbers
            // not a perfect solution due image size not taken into consideration, but is sufficient
            $results['black_white_pages'] -= $coloredPages;
            $results['total_pages'] = count($pages);
        } catch (Exception $e) {
            Log::error('Error analyzing PDF: ' . $e->getMessage());
            throw new Exception("Error analyzing PDF: " . $e->getMessage());
        }
        return $results;
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
