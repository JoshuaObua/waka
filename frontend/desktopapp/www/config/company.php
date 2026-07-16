<?php

$settingsPath = base_path('../settings.json');
$company = [
    'name' => 'Waka PMS',
    'tagline' => 'Property Management Solutions',
    'phone' => '',
    'email' => '',
    'address' => '',
    'favicon_url' => 'assets/images/favicon.ico',
];

if (file_exists($settingsPath)) {
    $settings = json_decode(file_get_contents($settingsPath), true);
    if (is_array($settings) && isset($settings['company_details'])) {
        $company = array_merge($company, $settings['company_details']);
    }
}

return $company;
