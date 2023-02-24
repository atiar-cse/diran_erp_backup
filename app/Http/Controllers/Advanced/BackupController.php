<?php

namespace App\Http\Controllers\Advanced;

// use Illuminate\Http\Request;
// use App\Http\Controllers\Controller;

use Artisan;
use Exception;
use Illuminate\Routing\Controller;
use League\Flysystem\Adapter\Local;
use Log;
use Request;
use Response;
use Storage;

class BackupController extends Controller
{
    public function index()
    {
        if (!count(config('backup.backup.destination.disks'))) {
            dd('No Disks Configured!');
        }

        $this->data['backups'] = [];

        foreach (config('backup.backup.destination.disks') as $disk_name) {
            $disk = Storage::disk($disk_name);
            $adapter = $disk->getDriver()->getAdapter();
            $files = $disk->allFiles();

            // make an array of backup files, with their filesize and creation date
            foreach ($files as $k => $f) {
                // only take the zip files into account
                if (substr($f, -4) == '.zip' && $disk->exists($f)) {
                    $this->data['backups'][] = [
                        'file_path'     => $f,
                        'file_name'     => str_replace('backups/', '', $f),
                        'file_size'     => $disk->size($f),
                        'last_modified' => $disk->lastModified($f),
                        'disk'          => $disk_name,
                        'download'      => ($adapter instanceof Local) ? true : false,
                        ];
                }
            }
        }

        // reverse the backups, so the newest one would be on top
        $this->data['backups'] = array_reverse($this->data['backups']);
        $this->data['title'] = 'Backups';

        return view('advanced.backup', $this->data);
    }

    public function create()
    {
        try {
            ini_set('max_execution_time', 600);

            Log::info('Backpack\BackupManager -- Called backup:run from admin interface');

            Artisan::call('backup:run');

            Log::info("Backpack\BackupManager -- backup process has started");
            
            session()->flash('msg_backup_create', 'Successfully backup created.');
            return back();
        } catch (Exception $e) {
            // Response::make($e->getMessage(), 500);
            $msg = $e->getMessage();
            session()->flash('msg_backup_create', $msg);
            return back();
        }
    }

    /**
     * Downloads a backup zip file.
     */
    public function download()
    {
        $disk = Storage::disk(Request::input('disk'));
        $file_name = Request::input('file_name');
        $adapter = $disk->getDriver()->getAdapter();

        if ($adapter instanceof Local) {
            $storage_path = $disk->getDriver()->getAdapter()->getPathPrefix();

            if ($disk->exists($file_name)) {
                return response()->download($storage_path.$file_name);
            } else {
                abort(404, 'Backup Doesnt Exist.');
            }
        } else {
            abort(404, 'Only Local Downloads Supported.');
        }
    }

    /**
     * Deletes a backup file.
     */
    public function delete()
    {
        $disk = Storage::disk(Request::input('disk'));
        $file_name = Request::input('file_name');

        if ($disk->exists($file_name)) {
            $disk->delete($file_name);

            return 'success';
        } else {
            return 'failed';
            abort(404, 'Backup Doesnt Exist');
        }
    }
}