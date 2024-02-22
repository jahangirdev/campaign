<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Contacts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;

class CSVContactImport implements ToCollection, WithHeadingRow
{
    private $list_id;

    public function __construct($list_id)
    {
        $this->list_id = $list_id;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            // Check if 'email' exists before creating a new Contact
            if (isset($row['email'])) {
                $existingContact = Contacts::where('email', $row['email'])->first();
                if(!$existingContact && $this->isValidEmail($row['email'])){
                    $contact = new Contacts([
                        "list_id" => $this->list_id,
                        "full_name" => $row['full_name'] ?? null,
                        "email" => $row['email'],
                        "phone" => $row['phone'] ?? null,
                        "country" => $row['country'] ?? null,
                        "address" => $row['address'] ?? null,
                    ]);

                    // Save the contact
                    $contact->save();
                }
            }
        }
    }

    private function isValidEmail($email)
    {
        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email',
        ]);

        return !$validator->fails();
    }
}
