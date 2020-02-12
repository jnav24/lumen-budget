@extends('layouts.email')

@section('content')
<tr>
    <td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box;">
        <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
            <tr>
                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" align="center">
                    <h1 style="font-family: sans-serif; font-size: 20px; font-weight: normal;">Verify New Device Access</h1>

                    <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin-bottom: 30px;">
                        An recent sign in attempt was made to your Dime Budget account from a new device or in a new location. As a security measure, we require additional confirmation before allowing access to your Dime Budget account.
                    </p>

                    <ul>
                        <li style="font-weight:bold;">IP Address: {{ $device->ip }}</li>
                        <li style="font-weight:bold;">Browser: {{ $device->agent }}</li>
                    </ul>

                    <p>
                        If this attempt was made by you, enter the code below to access your account on this device
                    </p>


                    <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; box-sizing: border-box;">
                        <tbody>
                        <tr>
                            <td align="center" style="font-family: sans-serif; font-size: 14px; vertical-align: top; padding-bottom: 30px;">
                                <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: auto;">
                                    <tbody>
                                    <tr>
                                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; background-color: #3498db; border-radius: 5px; text-align: center;">
                                            <a
                                                href="{{ env('WEBSITE_FULL_ADDRESS') . '/account-reset/' . $user->password_reset_token }}"
                                                target="_blank"
                                                style="display: inline-block; color: #ffffff; background-color: #3498db; border: solid 1px #3498db; border-radius: 5px; box-sizing: border-box; cursor: pointer; text-decoration: none; font-size: 14px; font-weight: bold; margin: 0; padding: 12px 25px; text-transform: capitalize; border-color: #3498db;">
                                                Reset Password
                                            </a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    </td>
</tr>
@endsection