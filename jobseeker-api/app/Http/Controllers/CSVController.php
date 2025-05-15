<?php

namespace App\Http\Controllers;

use App\Exports\BlogsExport;
use App\Exports\JobsExport;
use App\Exports\UsersExport;
use App\Imports\BlogsImport;
use App\Imports\JobsImport;
use App\Imports\UsersImport;
use App\Services\JobService;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class CSVController extends Controller
{
    public function jobExportHeader()
    {
        return Excel::download(new JobsExport(auth()->user()->isSuperadmin()), 'job-export-template.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function jobImport(Request $request)
    {
        $import = new JobsImport(auth()->user()->isSuperadmin(), app(JobService::class));
        $import->import($request->file('file'));

        return response()->json([
            'success' => true,
            'message' => 'Berhasil import CSV!',
        ], 200);
    }

    public function userExportHeader()
    {
        return Excel::download(new UsersExport, 'user-export-template.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function userImport(Request $request)
    {
        $import = new UsersImport;
        $import->import($request->file('file'));

        return response()->json([
            'success' => true,
            'message' => 'Berhasil import CSV!',
        ], 200);
    }

    public function blogExportHeader()
    {
        return Excel::download(new BlogsExport, 'blog-export-template.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function blogImport(Request $request)
    {
        $import = new BlogsImport();
        $import->import($request->file('file'));

        return response()->json([
            'success' => true,
            'message' => 'Berhasil import CSV!',
        ], 200);
    }
}
