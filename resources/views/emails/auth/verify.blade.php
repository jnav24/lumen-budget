@extends('layouts.email')

@section('content')
    <tr>
        <td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; text-align: left">
            <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
                <tr>
                    <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; text-align: left" align="center">
                        <div style="text-align: center">
                            <div style="margin: 0 auto; border: 2px solid #444; border-radius: 50%;  padding-top: 10px; height: 50px; width: 60px">
                                <img src="https://dimebudget.app/img/icon-device.png" alt="">
                            </div>
                        </div>
                        <h1 style="font-family: sans-serif; font-size: 20px; font-weight: normal; text-align: center; padding-bottom: 20px; margin-bottom: 40px; border-bottom: 1px solid #bbb;">Verify New Device Access</h1>

                        <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin-bottom: 30px;">
                            An recent sign in attempt was made to your Dime Budget account from a new device or in a new location. As a security measure, we require additional confirmation before allowing access to your Dime Budget account.
                        </p>

                        <ul style="list-style-type: none; padding: 0;">
                            <li style="font-weight:bold;">IP Address: {{ $device->ip }}</li>
                            <li style="font-weight:bold;">Browser: {{ $device->agent }}</li>
                        </ul>

                        <p>
                            If this attempt was made by you, enter the code below to access your account on this device
                        </p>


                        <table border="0" cellpadding="0" cellspacing="0" class="" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; box-sizing: border-box;">
                            <tbody>
                            <tr>
                                <td align="center" style="font-family: sans-serif; font-size: 14px; vertical-align: top; padding-bottom: 30px;">
                                    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: auto;">
                                        <tbody>
                                        <tr>
                                            <td style="font-family: sans-serif; font-size: 40px; vertical-align: top; border-radius: 5px; text-align: center;">
                                                {{ $device->verify_secret }}
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