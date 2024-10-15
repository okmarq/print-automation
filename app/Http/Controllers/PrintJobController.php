<?php

namespace App\Http\Controllers;

use App\Jobs\AnalyzePrintJob;
use App\Models\PrintJob;
use App\Http\Requests\StorePrintJobRequest;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\String\Slugger\AsciiSlugger;

class PrintJobController extends Controller
{
    public function index(): View|Factory|Application
    {
        $user = Auth::user();
        $printJobs = $user->hasRole(config('constants.role.admin')) ? PrintJob::all() : $user->printJobs()->orderBy('created_at', 'desc')->get();
        return view('pages.print.index', [
            'printJobs' => $printJobs
        ]);
    }

    public function create(): View|Factory|Application
    {
        return view('pages.print.upload');
    }

    /**
     * @throws Exception
     */
    public function store(StorePrintJobRequest $request): RedirectResponse
    {
        try {
            $user = Auth::user();
            $directoryPath = 'uploads/print/' . (new AsciiSlugger)->slug($user->email);
            if (!Storage::disk('public')->exists($directoryPath)) Storage::disk('public')->makeDirectory($directoryPath);
            $filePath = Storage::disk('public')->putFile($directoryPath, $request->file('file'));
            AnalyzePrintJob::dispatch($filePath, $user);
            return redirect()->route('print.upload')->with('success', 'File uploaded successfully. We will send you an email with your quotation once it has been analyzed.');
        } catch (Exception $e) {
            Log::debug($e->getMessage());
            return redirect()->route('print.create')->with('error', 'Something went wrong.');
        }
    }

    // For further functionality,
    // A controller caller PrintPress can be created
    // to programmatically performing the printing by another admin if required
}
