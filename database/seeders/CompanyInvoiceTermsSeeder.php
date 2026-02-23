<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanyInvoiceTermsSeeder extends Seeder
{
    /**
     * Default invoice terms and conditions (Gujarati) for the first company.
     */
    public function run(): void
    {
        $terms = '<p><strong>સૂચના તથા ખાસ નોંધ</strong></p>'
            . '<p>- વોરંટી માટે બિલ સાચવીને રાખવું</p>'
            . '<p>- કોઈપણ પ્રોડક્ટની વોરંટી કંપનીના નિયમ પ્રમાણે રહેશે</p>'
            . '<p>- કોઈપણ એપ્સનિ જવાબદારી કંપનીની રહેશે નહિ</p>'
            . '<p>- કોઈપણ પ્રોડક્ટમાં ભાંગતૂટ, વોટર ડેમેજ કે બર્નિંગની વોરંટી નહિ આવે</p>'
            . '<p>- વેચેલો માલ પરત લેવામાં આવશે નહિ</p>'
            . '<p>- પ્રોડક્ટની વોરંટી અને માહિતી કસ્ટમર કેર નંબર ૯૮૭૫૧૪૯૮૭૫ માં મેસેજ અથવા કોલ કરવાનો રહેશે</p>'
            . '<p>ઇલેક્ટ્રોનિક્સ વસ્તુઓની જાળવણી અને વધુ આયુષ્ય માટે નીચેની બાબતો ધ્યાનમાં રાખવી</p>'
            . '<p>- વોલ્ટેજ વધઘટનો પ્રોબ્લમ હોય તો સ્ટેબિલાઇઝરનો ઉપયોગ કરવો</p>'
            . '<p>- વરસાદી વાતાવરણમાં ઉપયોગ ન હોય ત્યારે પ્લગ માંથી પિન કાઢેલી રાખવી</p>'
            . '<p>- ટી.વી. માં સારી કંપનીના સેટઅપ બોક્સ વાપરવા વધુ હિતાવહ છે</p>';

        $company = Company::first();
        if ($company) {
            $company->update(['invoice_terms_and_conditions' => $terms]);
        } else {
            Company::create([
                'name' => 'Company',
                'invoice_terms_and_conditions' => $terms,
            ]);
        }
    }
}
