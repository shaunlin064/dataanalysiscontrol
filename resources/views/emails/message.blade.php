@extends('emails.layouts.master')

@section('content')
    <div class="block_div">
        <div class="block-grid  three-up" style='Margin: 0 auto; min-width: 320px; max-width: 600px; overflow-wrap: break-word; word-wrap: break-word; word-break: break-word;'>
            <div class="block_raw radius-top">
                <div class="col num12 block_col">
                    <div style="width:100% !important;">
                        <div class="col_div ptl_0" style="padding-top:0px; padding-bottom:5px;">
                            <div class="div_font div_padding_10" style="color:#0D0D0D;line-height:1.2;padding-top:10px;">
                                <div class="div_font" style="font-size: 12px; line-height: 1.2; color: #0D0D0D; mso-line-height-alt: 14px;">
                                    <p class="div_p" style="font-size: 28px; line-height: 1.2; mso-line-height-alt: 34px; margin: 0;"><span style="font-size: 28px;"><strong><span style="font-size: 28px;"></span></strong>
                                                        </span>
                                        <br/>
                                    <h1 style="color:#068485;font-family:'Proxima Nova',Roboto,'Open Sans',Arial,sans-serif;font-weight:normal;line-height:37px;font-size:30px;text-align: center;">Hi {{$name}}</h1>
                                    </p>
                                </div>
                            </div>
                            <div align="center" class="img-container center">
                            </div>
                            <div class="div_font div_padding_10" style="color:#555555;line-height: 1.5;padding-top:10px;">
                                <div sclass="div_font" tyle="font-size: 12px; line-height: 1.5; color: #555555; mso-line-height-alt: 18px;">
                                    <p class="div_p content_text">
                                </div>
                            </div>
                            <div class="div_font div_padding_10" style="color:#0D0D0D;line-height: 1.5;padding-top:20px;">
                                <div class="div_font" style="font-size: 12px; line-height: 1.5; color: #0D0D0D;mso-line-height-alt: 18px;">
                                    <p class="div_p content_text" style='font-size: 14px;line-height: 1.5;margin: 0;'>
                                        {{$title}}
                                    </p>
                                </div>
                            </div>
{{--                            <div align="center" class="button-container div_padding_10" style="padding-top:25px;">--}}
{{--                                <div class="div_font" style="text-decoration:none;display:inline-block;color:#ffffff;background-color:#a8bf6f;border-radius:4px;-webkit-border-radius:4px;-moz-border-radius:4px;width:auto; width:auto;;border-top:1px solid #a8bf6f;border-right:1px solid #a8bf6f;border-bottom:1px solid #a8bf6f;border-left:1px solid #a8bf6f;padding-top:15px;padding-bottom:15px;text-align:center;mso-border-alt:none;word-break:keep-all;">--}}
{{--                                    <span style="padding-left:15px;padding-right:15px;font-size:16px;display:inline-block;">--}}
{{--                                        <span style="font-size: 16px; line-height: 2; word-break: break-word; mso-line-height-alt: 32px;">--}}
{{--															<a href="" style="color:#fff">點我查看</a>--}}
{{--                                        </span>--}}
{{--                                    </span>--}}
{{--                                </div>--}}
{{--                            </div>--}}
                            <table class="table_fixed" border="0" cellpadding="0" cellspacing="0" class="divider ms_text" role="presentation" style="min-width: 100%;" valign="top" width="100%">
                                <tbody>
                                <tr style="vertical-align: top;" valign="top">
                                    <td class="divider_inner table_td ms_text" style="min-width: 100%; padding-top: 30px; padding-right: 10px; padding-bottom: 10px; padding-left: 10px;" valign="top">
                                        <table class="table_fixed" align="center" border="0" cellpadding="0" cellspacing="0" class="divider_content" role="presentation" style="border-top: 0px solid transparent; width: 100%;" valign="top" width="100%">
                                            <tbody>
                                            <tr style="vertical-align: top;" valign="top">
                                                <td class="table_td ms_text" valign="top"><span></span></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
