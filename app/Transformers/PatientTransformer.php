<?php

namespace App\Transformers;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Collection;

class PatientTransformer
{
    /**
     * Transform a patient model.
     */
    public function transform(Patient $patient)
    {
        return [
            'id' => $patient->id,
            'name' => $patient->user->name,
            'id_type' => $patient->user->id_type,
            'id_no' => $patient->user->id_no,
            'gender' => $patient->user->gender,
            'dob' => $patient->user->dob ? $patient->user->dob->format('Y-m-d') : null,
            'address' => $patient->user->address,
            'medium_acquisition' => $patient->medium_acquisition,
            'created_at' => $patient->created_at->toIso8601String(),
            'updated_at' => $patient->updated_at->toIso8601String(),
        ];
    }

    /**
     * Transform a collection of patients.
     */
    public function transformCollection(Collection $patients)
    {
        return $patients->map(function (Patient $patient) {
            return $this->transform($patient);
        });
    }
}