<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use App\Models\Student;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        $user = auth()->user();
        $canExport = $user->hasAnyRole(['Administrator', 'Petugas', 'Kepala', 'Bendahara Pondok', 'Bendahara Unit']);
        $canCreate = $user->hasAnyRole(['Administrator', 'Petugas']);

        $actions = [];

        if ($canExport) {
            $actions[] = Actions\Action::make('export_excel')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    $students = StudentResource::getEloquentQuery()->get();
                    return Excel::download(
                        new \App\Exports\StudentsExport($students),
                        'data-santri-' . now()->format('Y-m-d') . '.xlsx'
                    );
                });

            $actions[] = Actions\Action::make('download_documents')
                ->label('Download Berkas')
                ->icon('heroicon-o-folder-arrow-down')
                ->color('warning')
                ->action(function () {
                    $students = StudentResource::getEloquentQuery()->with('documents')->get();

                    $zipFileName = 'berkas-santri-' . now()->format('Y-m-d-His') . '.zip';
                    $zipPath = storage_path('app/temp/' . $zipFileName);

                    if (!file_exists(storage_path('app/temp'))) {
                        mkdir(storage_path('app/temp'), 0755, true);
                    }

                    $zip = new ZipArchive();
                    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                        throw new \Exception('Cannot create zip file');
                    }

                    foreach ($students as $student) {
                        $folderName = preg_replace('/[^a-zA-Z0-9\s]/', '', $student->full_name);
                        $folderName = trim($folderName);

                        foreach ($student->documents as $doc) {
                            $filePath = Storage::disk('local')->path($doc->file_path);
                            if (file_exists($filePath)) {
                                $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                                $docType = ucfirst(str_replace('_', ' ', $doc->type));
                                $fileName = "{$docType} - {$student->full_name}.{$extension}";
                                $zip->addFile($filePath, "{$folderName}/{$fileName}");
                            }
                        }
                    }

                    $zip->close();

                    return response()->download($zipPath)->deleteFileAfterSend(true);
                });
        }

        if ($canCreate) {
            $actions[] = Actions\CreateAction::make()
                ->label('Daftarkan Santri');
        }

        return $actions;
    }
}

