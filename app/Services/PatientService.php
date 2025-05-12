<?php

namespace App\Services;

use App\Repositories\Interfaces\PatientRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PatientService
{
    protected $patientRepository;

    public function __construct(PatientRepositoryInterface $patientRepository)
    {
        $this->patientRepository = $patientRepository;
    }

    /**
     * Get all patients
     */
    public function getAllPatients()
    {
        return $this->patientRepository->getAllPatients();
    }

    /**
     * Get patient by id
     */
    public function getPatientById($id)
    {
        return $this->patientRepository->getPatientById($id);
    }

    /**
     * Create a new patient
     */
    public function createPatient(array $data)
    {
        $this->validatePatientData($data);
        return $this->patientRepository->createPatient($data);
    }

    /**
     * Update an existing patient
     */
    public function updatePatient($id, array $data)
    {
        $this->validatePatientData($data);
        return $this->patientRepository->updatePatient($id, $data);
    }

    /**
     * Delete a patient
     */
    public function deletePatient($id)
    {
        return $this->patientRepository->deletePatient($id);
    }

    /**
     * Validate patient data
     */
    private function validatePatientData(array $data)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'id_type' => 'required|string|max:50',
            'id_no' => 'required|string|max:50',
            'gender' => 'required|in:male,female,other',
            'dob' => 'required|date',
            'address' => 'required|string',
            'medium_acquisition' => 'required|string|max:100',
            'email' => 'sometimes|nullable|email|max:255',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}