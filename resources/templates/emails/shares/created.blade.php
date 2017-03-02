<!doctype html>
<html>

<head>
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <title>{{ $subject }}</title>
    <style>
    /* CLIENT-SPECIFIC STYLES */
    body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
    table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
    img { -ms-interpolation-mode: bicubic; }


    body {
        font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;
        font-size:16px;
        line-height:1.5;

        color: #212529;
    }

    table {
        border: 0;
        border-collapse:separate;

        background-color:#fafafa;

        margin:0;
        padding:0;

        text-align:center;
        
        min-width:640px;
        width:100%;
    }

    a {
        color: #228AE6;
    }

    .footer {
        margin-top:24px;
    }

    .inner {
        width: 80%;
        background-color:#ffffff;
        text-align:left;
        padding:16px 24px;
        border:1px solid #868e96;
    }

    .klink-logo {
        color: #228AE6;
        font-weight: 700;
        text-align: center;
        padding: 24px;
    }
    
    /* iOS BLUE LINKS */
    a[x-apple-data-detectors] {
        color: inherit !important;
        text-decoration: none !important;
        font-size: inherit !important;
        font-family: inherit !important;
        font-weight: inherit !important;
        line-height: inherit !important;
    }
    
    /* ANDROID MARGIN HACK */
    body { margin:0 !important; }
    div[style*="margin: 16px 0"] { margin:0 !important; }
    
    @media only screen and (max-width: 639px) {
        body, #body {
            min-width: 320px !important;
        }
        table.wrapper {
            width: 100% !important;
            min-width: 320px !important;
        }
        table.wrapper > tbody > tr > td {
            border-left: 0 !important;
            border-right: 0 !important;
            border-radius: 0 !important;
            padding-left: 10px !important;
            padding-right: 10px !important;
        }
    }
    </style>
</head>

<body>

    <table>
        <tbody>
            <tr>
                <td style="margin:16px 0">
                    <span class="klink-logo">{{ trans('mail.logo_text', [], '', $language) }}</span>
                </td>
            </tr>
            <tr>
                <td align="center">
                    <table class="inner">
                        <tbody>
                            <tr>
                                <td>

                                    {{ trans( $is_collection ? 'mail.sharecreated.shared_collection_with_you' : 'mail.sharecreated.shared_document_with_you', ['user' => $name], '', $language ) }}
                                
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <table class="inner" style="border:0">
                                        <tr>
                                            <td>{{ trans('mail.sharecreated.title_label', [], '', $language) }}</td>

                                            <td><a href="{{ $share_link }}">{{ $title }}</a></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="footer"  style="margin:24px 0">
                    <div>
                        {!! trans('mail.footer_help', ['url' => config('app.url') . route('help', null, false)], '', $language) !!}
                    </div>
                    <div>
                        {!! trans('mail.footer_disclaimer', ['url' => config('app.url'), 'instance' => config('app.url')], '', $language) !!}
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    
    </body>
</html>