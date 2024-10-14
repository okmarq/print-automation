<?php

namespace App\Jobs;

use App\Mail\PrintQuoted;
use App\Models\AdminSetting;
use App\Models\Payment;
use App\Models\PrintJob;
use App\Models\User;
use App\Services\FileAnalyzerService;
use Exception;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class AnalyzePrintJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $filePath;
    protected User $user;

    /**
     * Create a new job instance.
     */
    public function __construct(string $filePath, User $user)
    {
        $this->filePath = $filePath;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $analyzer = new FileAnalyzerService();
            $result = $analyzer->analyze($this->filePath);
            $settings = AdminSetting::first();
            $amount = ($result['black_white_pages'] * $settings->cost_bw_page) + ($result['colored_pages'] * $settings->cost_color_page) + ($result['total_pixels'] * $settings->cost_pixel_image);
            $printJob = PrintJob::create([
                'total_pages' => $result['total_pages'],
                'total_images' => $result['total_images'],
                'colored_pages' => $result['colored_pages'],
                'black_white_pages' => $result['black_white_pages'],
                'total_pixels' => $result['total_pixels'],
                'amount' => $amount,
                'user_id' => $this->user->id,
                'file_path' => $this->filePath,
            ]);
            Payment::create([
                'print_job_id' => $printJob->id,
                'amount' => $printJob->amount,
                'status' => config('constants.status.unpaid')
            ]);
            // when ready send the email
//            Mail::to($this->user)->send(new PrintQuoted($printJob));
        } catch (Exception $e) {
            Log::error("Failed to analyze {$this->filePath}: " . $e->getMessage());
        }
    }
}
