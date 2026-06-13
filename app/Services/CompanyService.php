<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompanyService
{
    /**
     * Create a new company record.
     *
     * Handles logo upload (if provided) and stores
     * the company data inside a database transaction.
     */
    public function store(array $data): Company
    {
        return DB::transaction(function () use ($data) {

            // Upload company logo if available
            if (! empty($data['company_logo'])) {
                $data['company_logo'] = $this->uploadLogo(
                    $data['company_logo']
                );
            }

            // Create company record
            return Company::create($data);
        });
    }

    /**
     * Update an existing company record.
     *
     * Handles logo replacement (if a new logo is uploaded)
     * and updates company information inside a transaction.
     */
    public function update(Company $company, array $data): Company
    {
        return DB::transaction(function () use ($company, $data) {

            // Upload new logo and remove old logo if exists
            if (! empty($data['company_logo'])) {

                $data['company_logo'] = $this->uploadLogo(
                    $data['company_logo'],
                    $company->company_logo
                );
            }

            // Update company data
            $company->update($data);

            // Return fresh model instance
            return $company->fresh();
        });
    }

    /**
     * Delete a company record.
     *
     * Removes the associated logo file from storage
     * before deleting the company record.
     */
    public function destroy(Company $company): bool
    {
        return DB::transaction(function () use ($company) {

            // Delete logo file if it exists
            if (
                $company->company_logo &&
                Storage::disk('public')->exists($company->company_logo)
            ) {
                Storage::disk('public')->delete($company->company_logo);
            }

            // Delete company record
            return $company->delete();
        });
    }

    /**
     * Upload a company logo.
     *
     * Generates a unique filename using UUID,
     * optionally removes the previous logo,
     * and stores the new file in public storage.
     */
    private function uploadLogo(
        UploadedFile $file,
        ?string $oldLogo = null
    ): string {

        // Remove previous logo if available
        if (
            $oldLogo &&
            Storage::disk('public')->exists($oldLogo)
        ) {
            Storage::disk('public')->delete($oldLogo);
        }

        // Generate unique filename
        $filename = Str::uuid() . '.' . $file->extension();

        // Store file and return relative path
        return $file->storeAs(
            'companies/logo',
            $filename,
            'public'
        );
    }
}
