<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_brokerage_terms_versions', function (Blueprint $table) {
            $table->id();
            $table->string('version', 32)->unique();
            $table->string('title', 180);
            $table->text('introduction');
            $table->json('clauses');
            $table->timestamps();
        });

        $now = now();

        DB::table('seller_brokerage_terms_versions')->insert([
            'version' => '1.0',
            'title' => 'Seller Brokerage Agreement',
            'introduction' => 'Please read these terms before submitting your vehicle. They apply if HM CARS accepts your vehicle for brokerage.',
            'clauses' => json_encode([
                [
                    'title' => 'Brokerage service and commission',
                    'body' => 'HM CARS acts as a vehicle broker and may advertise the vehicle, introduce prospective buyers, and coordinate communications. If a sale to a buyer introduced by HM CARS is completed, you agree to pay HM CARS a commission of 2% of the final sale price. The commission is due within 24 hours after you receive the buyer’s cleared sale funds. Pay it to an HM CARS payment method confirmed in writing; HM CARS will provide a receipt. HM CARS will not deduct commission from buyer funds unless you separately authorize that arrangement in writing.',
                ],
                [
                    'title' => 'Your authority and disclosures',
                    'body' => 'You confirm that you are the vehicle’s lawful owner or have documented authority to sell it. You must provide accurate, complete information and promptly disclose known defects, accident or repair history, mileage or odometer concerns, and any other material information about the vehicle. You must also disclose any loan, logbook financing, lien, or other restriction that may affect the sale or transfer.',
                ],
                [
                    'title' => 'Verification and vehicle condition',
                    'body' => 'HM CARS may inspect the vehicle and request identification, ownership, financing, or other supporting documents before or during a listing. Any checks HM CARS chooses to make are preliminary and limited to the inspection, documents, and information actually available. A listing or check is not a guarantee that the vehicle is roadworthy, free of debt or restrictions, or legally transferable. You remain responsible for truthful disclosures and supporting your ownership and authority; the buyer remains responsible for their own inspection and due diligence.',
                ],
                [
                    'title' => 'Price and sale approval',
                    'body' => 'Your asking price is not a confirmed sale price. HM CARS may communicate offers, but no final selling price is binding unless you approve it in writing, including by a message that can be retained. Do not complete a sale at a different price without confirming the commission due on that price.',
                ],
                [
                    'title' => 'Payment and transfer',
                    'body' => 'You and the buyer must agree on and keep a verifiable record of the payment method, amount, and confirmation. Do not rely solely on screenshots or unverified payment messages as proof of cleared funds. You and the buyer are responsible for completing the applicable NTSA ownership transfer process, providing requested information, verifying the transfer, and signing any separate sale documents. HM CARS can coordinate communication but does not complete or guarantee the transfer on either party’s behalf.',
                ],
                [
                    'title' => 'Withdrawal, cancellation, and introduced buyers',
                    'body' => 'You may ask HM CARS to withdraw the listing, and a buyer may cancel before a sale is completed; no brokerage commission is due where no qualifying sale is completed. If, within six months after HM CARS introduces a buyer to you, you or someone acting for you completes a sale of this vehicle to that buyer, the 2% commission remains due even if you withdrew the listing or completed the sale without HM CARS. Tell HM CARS promptly if a buyer cancels or the vehicle is no longer available.',
                ],
                [
                    'title' => 'Disputes and applicable law',
                    'body' => 'You and HM CARS will first try in good faith to resolve a dispute through direct discussion. If it remains unresolved, it will be governed by the laws of Kenya and may be referred to a competent Kenyan court.',
                ],
                [
                    'title' => 'Privacy and documents',
                    'body' => 'HM CARS will use the contact details and documents you provide to review and verify the submission, communicate with you, facilitate an approved listing or transaction, and address related disputes or legal obligations. Access will be limited to people who need the information for these purposes. Do not submit documents unrelated to the vehicle or transaction; contact HM CARS to ask about access, correction, or deletion where applicable.',
                ],
            ], JSON_THROW_ON_ERROR),
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_brokerage_terms_versions');
    }
};
