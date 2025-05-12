<?php

namespace App\Repositories\Eloquent;

use App\Models\Patient;
use App\Models\User;
use App\Repositories\Interfaces\PatientRepositoryInterface;
use Illuminate\Support\Facades\DB;

class PatientRepository implements PatientRepositoryInterface
{
    protected $patient;
    protected $user;

    public function __construct(Patient $patient, User $user)
    {
        $this->patient = $patient;
        $this->user = $user;
    }

    public function getAllPatients()
    {
        return $this->patient->with('user')->get();
    }

    public function getPatientById($id)
    {
        return $this->patient->with('user')->find($id); 
    }

    public function createPatient(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = $this->user->create([
                'name' => $data['name'],
                'email' => $data['email'] ?? $this->generateTempEmail($data['name']),
                'password' => bcrypt(str_random(16)),
                'id_type' => $data['id_type'],
                'id_no' => $data['id_no'],
                'gender' => $data['gender'],
                'dob' => $data['dob'],
                'address' => $data['address'],
            ]);

            $patient = $this->patient->create([
                'user_id' => $user->id,
                'medium_acquisition' => $data['medium_acquisition'],
            ]);

            $patient->user = $user;
            
            return $patient;
        });
    }

    public function updatePatient($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $patient = $this->patient->find($id);
            if (!$patient) return null;
            
            $patient->update([
                'medium_acquisition' => $data['medium_acquisition'],
            ]);

            $patient->user->update([
                'name' => $data['name'],
                'id_type' => $data['id_type'],
                'id_no' => $data['id_no'],
                'gender' => $data['gender'],
                'dob' => $data['dob'],
                'address' => $data['address'],
            ]);

            return $this->getPatientById($id);
        });
    }

    public function deletePatient($id)
    {
        $patient = $this->patient->find($id);
        if (!$patient) return null;
        $userId = $patient->user_id;
        
        return DB::transaction(function () use ($patient, $userId) {
            $patient->delete();
            $this->user->findOrFail($userId)->delete();
            
            return true;
        });
    }

    /**
     * Generate a temporary email for users without an email
     */
    private function generateTempEmail($name)
    {
        $slug = strtolower(str_replace(' ', '.', $name));
        return $slug . '.' . uniqid() . '@example.com';
    }
}