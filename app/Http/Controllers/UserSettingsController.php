<?php

namespace App\Http\Controllers;

use App\Notebook;
use App\UserSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserSettingsRequest;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use ZipArchive;

class UserSettingsController extends Controller {

	/**
	 * Show user settings form.
	 *
	 */
	public function show() {
		$user_settings = UserSettings::get(Auth::id());

		return view('user_settings', [
			'font_size' => $user_settings->font_size,
			'font_family' => $user_settings->font_family,
			'line_height' => $user_settings->line_height,
			'paragraph_margin' => $user_settings->paragraph_margin,
		]);
	}

	/**
	 * Save user settings form.
	 * Request is validated by UserSettingsRequest
	 *
	 */
	public function submit(UserSettingsRequest $request) {
		// User
		$user = Auth::user();
		$user->email = $request->email;

		// Change Password
		if ($request->filled('new_password')) {
			// Update password
			$user->password = Hash::make($request->new_password);
		}

		$user->save();


		// Editor Settings
		$user_settings = UserSettings::firstOrNew(['user_id' => Auth::id()]);
		$user_settings->font_size = $request->font_size;
		$user_settings->font_family = $request->font_family;
		$user_settings->line_height = $request->line_height;
		$user_settings->paragraph_margin = $request->paragraph_margin;
		$user_settings->save();

		// Return
		return redirect()->back()->with('success', 'Settings are successfully saved.');
	}

	/**
	 * Export users notebooks and notes as a zip
	 *
	 */
	public function export(Request $request) {
		$user = $request->user();

		if (!$user) {
			return abort(401);
		}

		$zip = new ZipArchive();

		// Creates a temporary file that will be deleted after execution is completed
		$temp = tempnam(sys_get_temp_dir(), 'DOP');

		// Open up the zip file for writing
		$zip->open($temp, ZipArchive::CREATE);

		// Add a folder for the trashed items
		$trashFolder = 'trash';
		$zip->addEmptyDir($trashFolder);

		// Get all the notebooks associated to the user - we are loading it in a chunked way
		// so that it saves the memory a bit if there is someone with a lot of notes
		Notebook::where('user_id', '=', $user->id)->with(['notes' => function ($query) {
			$query->withTrashed();
		}])->chunk(200, function ($notebooks) use ($zip, $trashFolder) {
			foreach ($notebooks as $notebook) {
				// Add a new folder for the notebook in the zip file
				$name = $notebook->title;
				$zip->addEmptyDir($name);

				// Add each of the notes as a html file
				foreach ($notebook->notes as $note) {
					$fileName = Str::slug($note->title).'.html';
					$folder = $name;

					// If the note is trashed, then we will add it to that folder
					if ($note->deleted_at) {
						$folder = "$trashFolder/$folder";
					}

					// Just append the title to the note - if need more fancier stuff going on
					// should just use a blade template for it
					$content = "<h1>$note->title</h1>".$note->content;

					// Finally add the file to the zip folder
					$zip->addFromString("$folder/$fileName", $content);
				}
			}
		});

		$zip->close();

		// Return the zip file, and delete it afterwards
		$fileName = 'dopenote_archive_'.date('Y-m-d').'.zip';
		return response()->download($temp, $fileName, [
			'Content-Type' => 'application/zip',
		])->deleteFileAfterSend();
	}
}
