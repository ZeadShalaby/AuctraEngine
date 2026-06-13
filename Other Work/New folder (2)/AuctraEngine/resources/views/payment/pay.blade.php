<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة دفع معاملات</title>
    <style>
        body {
            font-family: sans-serif;
            text-align: center;
            padding-top: 100px;
            background: #f1f5f9;
        }

        .box {
            background: white;
            max-width: 400px;
            margin: 0 auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .btn {
            background: #0284c7;
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 18px;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>

    <script src="{{ $jsUrl }}"></script>
</head>

<body>

    <div class="box">
        <h2>بوابة معاملات الإلكترونية</h2>
        <p>رقم الفاتورة: <strong>{{ $ref }}</strong></p>
        <p>المبلغ: <strong>{{ $amount / 1000 }} دينار ليبي</strong></p>

        <button type="button" class="btn" onclick="pay()">اضغط للدفع الآن</button>
    </div>

    <script>
        function pay() {
            if (typeof Lightbox === 'undefined' || !Lightbox.Checkout) {
                alert('جاري الاتصال بالسيرفر الآمن للمصرف، انتظر ثانية وأعد الضغط.');
                return;
            }

            // التعديل الحركي طبقاً للدوكيومنتيشن الجديدة: مساواة = بدلاً من أقواس دالة ()
            Lightbox.Checkout.configure = {
                MID: "{{ $mid }}",
                TID: "{{ $tid }}",
                AmountTrxn: "{{ $amount }}",
                MerchantReference: "{{ $ref }}",
                TrxDateTime: "{{ $dateTime }}",
                SecureHash: "{{ $secureHash }}",

                completeCallback: function(data) {
                    window.location.href =
                        "{{ url('/api/payment/callback') }}?MerchantReference={{ $ref }}&Status=Approved";
                },
                cancelCallback: function() {
                    window.location.href = "{{ url('/cancel') }}";
                },
                errorCallback: function(error) {
                    window.location.href = "{{ url('/fail') }}";
                }
            };
            // تشغيل النافذة المنبثقة
            Lightbox.Checkout.showLightbox();
        }
    </script>
    <script src="https://tnpg.moamalat.net:6006/js/lightbox.js"></script>
</body>

</html>
