<?php

namespace App\Http\Controllers\Api\Coach;

use Exception;
use Illuminate\Http\Request;
use App\Models\CoachCertificate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class CertificateController extends Controller
{
    public function storeOrUpdate(Request $request, $certificateId = NULL)
    {
        try {
            $this->validate($request, [
                "file" => 'required',
            ]);

            $userId = Auth::user()->id;

            if ($certificateId) {
                $certificate = CoachCertificate::findOrfail($certificateId)->first();
                if ($request->hasFile('file')) {
                    if ($certificate->file && File::exists(public_path($certificate->file))) {
                        File::delete(public_path($certificate->file));
                    }

                    $directory = 'images/certificates';
                    $directoryPath = public_path($directory);

                    if (!File::exists($directoryPath)) {
                        File::makeDirectory($directoryPath, 0755, true);
                    }

                    $file = $request->file('file');
                    $fileName = time() . '.' . $file->getClientOriginalExtension();
                    $file->move($directoryPath, $fileName);

                    $certificate->file = $directory . '/' . $fileName;
                }
                $certificate->update();
            } else {
                $certificate = new CoachCertificate();
                $certificate->user_id = $userId;
                if ($request->hasFile('file')) {

                    $directory = 'images/certificates';
                    $directoryPath = public_path($directory);

                    if (!File::exists($directoryPath)) {
                        File::makeDirectory($directoryPath, 0755, true);
                    }

                    $file = $request->file('file');
                    $fileName = time() . '.' . $file->getClientOriginalExtension();
                    $file->move($directoryPath, $fileName);

                    $certificate->file = $directory . '/' . $fileName;
                }
                $certificate->save();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Certificate saved successfully!',
                'certificate' => $certificate
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!',
            ], 500);
        }
    }

    public function view($certificateId = null)
    {
        try {
            if ($certificateId) {
                $certificate = CoachCertificate::findOrFail($certificateId);
                $certificate->file = url($certificate->file);

                return response()->json([
                    'status' => 'success',
                    'certificate' => $certificate
                ], 201);
            }

            $certificates = CoachCertificate::where('user_id', Auth::id())->get();

            foreach ($certificates as $certificate) {
                $certificate->file = url($certificate->file);
            }

            return response()->json([
                'status' => 'success',
                'certificates' => $certificates
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
