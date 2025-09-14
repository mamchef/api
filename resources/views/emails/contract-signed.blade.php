@php
    $header_title = 'Contract Signed Successfully';
    $greeting = 'Hello ' . ($chef->getFullName() ?? 'Partner') . ',';
    $message = 'Great news! Your partner agreement has been successfully signed and processed.';

    $highlight_message = 'You can now start accepting orders and earning with MamChef!';
    $highlight_type = 'success';

    $button_text = 'View Dashboard';
    $button_url = 'https://app.mamchef.com/chef/dashboard';

    $additional_content = 'Thank you for joining MamChef as our trusted partner. We look forward to a successful collaboration!';
@endphp

@include('emails.template')