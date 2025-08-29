{{-- resources/views/mails/auth/account-created.blade.php --}}
@extends('emails.master')

@section('page-content')
    <div style="padding: 0 5%; margin-right:auto; margin-left:auto" class="responsive">
        <div style="width: 100%; text-align: center; margin-top: 20px;">
            <div
                style="display: inline-flex; align-items: center; background: #49a734; border-radius: 85.91px; padding: 4.436px 10.309px; text-transform: capitalize;">
                <img src="{{ asset('assets/emails/security-lock.png') }}" alt="Security Lock Icon"
                    style="vertical-align: middle; margin-right:8px">
                <span
                    style="color: #FFF; font-family: Montserrat; font-size: 14px; font-style: normal; font-weight: 400; line-height: 14px;">
                    Account Creation
                </span>
            </div>
        </div>

        <div style="width: 100%; text-align: center;">
            <img src="{{ asset('assets/emails/welcome_.png') }}" class="cover-image" alt="Welcome Mail"
                style="max-height: 400px; display: block; margin: 0 auto;">
        </div>

        <div style="width: 100%; display: table; margin: 0 auto;">
            <p class="unique-key-text"
                style="color: #252525; text-align: center; font-family: Montserrat; font-size: 15px; font-weight: 300;">
                Hi {{ $guardian->user->first_name }},<br>
                We are pleased to receive you as a parent of duplicationhub School under the following students:
            </p>
        </div>

        <table class="custom-table w-100 table table-hover"
            style="width:100%; border-collapse:collapse; font-family:Montserrat; font-size:16px; margin:5px auto; padding:10px"
            width="100%">
            <thead style="color:#5063F4; font-weight:700; background-color:#EFF2FE" bgcolor="#EFF2FE">
                <tr>
                    <th style="padding:0.6rem; text-align:start; border-bottom:3px solid white;">Full Name</th>
                    <th style="padding:0.6rem; text-align:start; border-bottom:3px solid white;">Admission No</th>
                    <th style="padding:0.6rem; text-align:start; border-bottom:3px solid white;">Date Of Admission</th>
                </tr>
            </thead>
            <tbody style="background-color:#F4F5F6;" bgcolor="#F4F5F6">
                @foreach ($guardian->students as $student)
                    <tr>
                        <td style="padding:0.6rem; text-align:start; color:#252525; font-weight:700; border-bottom:3px solid white;">
                            {{ $student->user->first_name }} {{ $student->user->middle_name }} {{ $student->user->last_name }}
                        </td>
                        <td style="padding:0.6rem; text-align:start; color:#252525; font-weight:700; border-bottom:3px solid white;">
                            {{ $student->student_admission_number }}
                        </td>
                        <td style="padding:0.6rem; text-align:start; color:#252525; font-weight:700; border-bottom:3px solid white;">
                            {{ $student->date_of_admission }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p style="font-family: Montserrat; font-size: 15px; text-align: center; margin-top: 20px;">
            An account has also been created to access the parents app.
        </p>
        <p style="font-family: Montserrat; font-size: 15px; text-align: center;">
            Please use your email as the username and the password <strong>{{ $password }}</strong> to login and set a new password.
        </p>

        <div style="width: 100%; text-align: center; margin-top: 30px;">
            <a href="{{ url('login') }}"
                style="color: #49a734; font-family: Montserrat; font-size: 16px; text-decoration: underline;">
                Log In
            </a>
        </div>
    </div>
@endsection
