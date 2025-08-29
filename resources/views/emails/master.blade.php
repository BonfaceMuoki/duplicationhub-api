<!DOCTYPE html>
<html lang="en"
    style="box-sizing: border-box;font-family: sans-serif;line-height: 1.15;-webkit-text-size-adjust: 100%;-webkit-tap-highlight-color: transparent;">

<head style="box-sizing: border-box;">
    <meta charset="UTF-8" style="box-sizing: border-box;">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" style="box-sizing: border-box;">
    <meta http-equiv="X-UA-Compatible" content="ie=edge" style="box-sizing: border-box;">


    <link rel="preconnect" href="https://fonts.googleapis.com" style="box-sizing: border-box;">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin style="box-sizing: border-box;">

    <title style="box-sizing: border-box;"></title>
    <style>
        @media only screen and (min-width: 768px) {
           .responsive {
               max-width: 550px;
           }
        
       }
       @media only screen and (max-width: 767px) {
           .responsive {
               max-width: 550px;
           }
        }
        @media only screen and (min-width: 768px) {
           .responsive {
               max-width: 550px;
           }
       }
       @media only screen and (min-width: 1024px) {
           .responsive {
               max-width: 650px;
           }
       }
   
       @media only screen and (min-width: 1536px) {
           .responsive {
               max-width: 800px;
           }
       }
       .a6S{display:none !important;}
       .a5q{
        display: none !important;
       }
   </style>
</head>

<body>
    <p>
        @yield('styles')


    </p>
    <div class="container my-4"
        style="box-sizing: border-box;width: 100%;padding-right: 15px;padding-left: 15px;margin-right: auto;margin-left: auto;margin-top: 1.5rem!important;margin-bottom: 1.5rem!important;min-width: 992px!important;">
        <div class="w-100 d-flex justify-content-center"
            style="box-sizing: border-box; display: flex !important; -ms-flex-pack: center !important; justify-content: center !important; width: 100% !important;">
            <img src="{{ asset('assets/emails/logo.png') }}" alt="" class="mx-auto d-block"
                style="box-sizing: border-box; vertical-align: middle; border-style: none; page-break-inside: avoid;margin:auto;height: 49.486px;">
        </div>

        <div class="w-100"
            style="box-sizing: border-box; display: table; width: 100%; margin-bottom: .5rem;">
            <div style="box-sizing: border-box; display: table-cell; text-align: center; vertical-align: middle;">
                @yield('page-content')
            </div>
        </div>



        <div class="footer" style="box-sizing: border-box;">
            <div style="width: 100%; display: table; margin: 0 auto;">
                <div class="cta-icons"
                    style="box-sizing: border-box; gap: 1rem; display: table-cell; text-align: center; vertical-align: middle;">
                    <a href="https://linkedin.com/company/ewob" target="_blank"
                        style="box-sizing: border-box; color: #007bff; text-decoration: underline; background-color: transparent; padding-right:10px;">
                        <img src="{{ asset('assets/emails/linkedin.png') }}" style="height: 30px ;width :30px"
                            alt="" srcset="">

                    </a>
                    <a href="mailto:info@duplicationhub.ac.ke" target="_blank"
                        style="box-sizing: border-box; color: #007bff; text-decoration: underline; background-color: transparent;">
                        <img src="{{ asset('assets/emails/email.png') }}" style="height: 30px ;width :30px"
                            alt="" srcset="">
                    </a>
                </div>
            </div>
            <div style="width: 100%; display: table; margin: 0 auto;">
                <p
                    style="box-sizing: border-box; margin-top: 0; margin-bottom: 1rem; orphans: 3; widows: 3; text-align: center;">
                    Not Sure why you received this email?
                    <span
                        style="box-sizing: border-box; color: #5063F4; font-family: Montserrat; font-size: 16px; font-style: normal; font-weight: 700; line-height: normal; text-decoration-line: underline;">
                        <a class="links" href="mailto:info@duplicationhub.ac.ke"
                            style="color:#5063F4; font-family:Montserrat; font-size:16px; font-style:normal; font-weight:700; line-height:normal; text-decoration-line:underline; background-color:transparent; box-sizing:border-box; text-decoration:underline">Email</a>
                    </span> us or use our
                   
                </p>
            </div>
            <div style="margin: 0 auto; width: 80%; text-align: center;">
                <div style="display: inline-block; margin-right: 20px;">
                    © {{ Date('Y') }} <a href="https://duplicationhub.ac.ke/" class="links"
                        style="color:#5063F4; font-family:Montserrat; font-size:16px; font-style:normal; font-weight:700; line-height:normal; text-decoration-line:underline; background-color:transparent; text-decoration:underline">
                        duplicationhub School</a>
                </div>
                <div style="display: inline-block;">
                    <!-- <a href="https://duplicationhub.ac.ke" class="links"
                        style="color:#5063F4; font-family:Montserrat; font-size:16px; font-style:normal; font-weight:700; line-height:normal; text-decoration-line:underline; background-color:transparent; margin-left:20px; text-decoration:underline">FAQ</a>
                 -->
                    </div>
            </div>




        </div>



        <script style="box-sizing: border-box;"></script>
    </div>
</body>

</html>