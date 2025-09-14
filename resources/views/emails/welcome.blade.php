@extends('emails.template')

@section('content')
    @php
        $header_title = 'Welcome to MamChef!';
        $greeting = 'Hello ' . ($user->name ?? 'Chef') . ',';
        $message = 'We\'re excited to have you join the MamChef community! Your culinary journey with us starts now.';

        $highlight_message = 'Your account has been successfully created and is ready to use.';
        $highlight_type = 'success';

        $button_text = 'Get Started';
        $button_url = 'https://app.mamchef.com/dashboard';

        $additional_content = 'If you have any questions, our support team is here to help. Welcome aboard!';
    @endphp
@endsection