<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat - {{ $winners['juara1'] }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #1e3a8a;
            --gold-light: #f6e0b5;
            --gold-dark: #c5a059;
            --gold-gradient: linear-gradient(135deg, #c5a059 0%, #f6e0b5 50%, #c5a059 100%);
            --text-grey: #4b5563;
            --light-grey: #9ca3af;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
            font-family: 'Montserrat', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .certificate-page {
            width: 297mm;
            height: 210mm;
            background: #fffdfa; /* Warm off-white */
            position: relative;
            box-sizing: border-box;
            box-shadow: 0 10px 60px rgba(0,0,0,0.15);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            padding: 40px 70px 80px 70px; /* Increased bottom padding to 80px */
            margin: auto;
        }

        /* Watermark */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 140px;
            font-weight: 900;
            color: rgba(30, 58, 138, 0.03);
            z-index: 1;
            pointer-events: none;
            white-space: nowrap;
            text-transform: uppercase;
        }

        /* Border Utama Ganda */
        .main-border {
            position: absolute;
            top: 25px;
            left: 25px;
            right: 25px;
            bottom: 25px;
            border: 3px double var(--gold-dark);
            pointer-events: none;
            z-index: 10;
        }

        .inner-border {
            position: absolute;
            top: 40px;
            left: 40px;
            right: 40px;
            bottom: 40px;
            border: 1px solid rgba(197, 160, 89, 0.3);
            pointer-events: none;
            z-index: 10;
        }

        /* Ornamen Emas Siku */
        .corner-accent {
            position: absolute;
            width: 110px;
            height: 110px;
            border: 8px solid var(--gold-dark);
            z-index: 11;
        }

        .top-left {
            top: 15px;
            left: 15px;
            border-right: none;
            border-bottom: none;
        }

        .bottom-right {
            bottom: 15px;
            right: 15px;
            border-left: none;
            border-top: none;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 20;
            margin-bottom: 10px;
            height: 90px;
        }

        .brand-logo, .school-logo {
            display: flex;
            align-items: center;
        }

        .content {
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            z-index: 20;
            margin-top: -15px;
        }

        .title-main {
            font-family: 'Playfair Display', serif;
            font-size: 85px;
            color: var(--primary-blue);
            margin: 0;
            letter-spacing: 12px;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.05);
        }

        .title-sub {
            font-size: 18px;
            color: var(--gold-dark);
            text-transform: uppercase;
            letter-spacing: 6px;
            margin-top: 5px;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .intro-text {
            font-size: 20px;
            color: var(--text-grey);
            margin-bottom: 25px;
            font-style: italic;
        }

        .winner-name {
            font-family: 'Playfair Display', serif;
            font-size: 68px;
            background: var(--gold-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-style: italic;
            margin-bottom: 20px;
            font-weight: 700;
            filter: drop-shadow(0 2px 2px rgba(0,0,0,0.1));
        }

        .divider {
            width: 40%;
            height: 2px;
            background: var(--gold-gradient);
            margin: 0 auto 25px auto;
        }

        .description {
            font-size: 19px;
            color: var(--text-grey);
            line-height: 1.5;
            max-width: 850px;
            margin: 0 auto;
        }

        .description strong {
            color: var(--primary-blue);
            font-weight: 800;
        }

        .event-name {
            font-size: 28px;
            color: var(--primary-blue);
            font-weight: 800;
            margin-top: 15px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .footer {
            display: flex;
            justify-content: space-between;
            padding: 0 60px;
            margin-top: 20px; /* Reduced from 40px */
            z-index: 20;
            position: relative;
        }

        /* Gold Seal Placeholder */
        .gold-seal {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 100px;
            height: 100px;
            background: var(--gold-gradient);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 5px 15px rgba(197, 160, 89, 0.4);
            border: 2px solid #fff;
        }
        .gold-seal::after {
            content: 'AWARD';
            color: #fff;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .sig-block {
            text-align: center;
            width: 280px;
        }

        .sig-title {
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 50px; /* Reduced from 70px */
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .sig-name {
            font-weight: 800;
            color: var(--primary-blue);
            font-size: 19px;
            text-transform: uppercase;
            border-bottom: 2px solid var(--gold-dark);
            padding-bottom: 4px;
            display: inline-block;
            min-width: 220px;
        }

        .sig-subtitle {
            font-size: 13px;
            color: var(--gold-dark);
            font-style: italic;
            margin-top: 5px;
            font-weight: 600;
        }

        @media print {
            @page {
                size: A4 landscape;
                margin: 0;
            }
            body { 
                background: none; 
                margin: 0;
                padding: 0;
            }
            .certificate-page { 
                box-shadow: none; 
                margin: 0;
                width: 297mm;
                height: 210mm;
                page-break-after: always;
                -webkit-print-color-adjust: exact;
                border: none;
                background: #fffdfa !important;
            }
            .btn-print { display: none; }
        }

        .btn-print {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: var(--primary-blue);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            z-index: 100;
            transition: all 0.3s;
        }

        .btn-print:hover { transform: translateY(-3px); }
    </style>
</head>
<body>

    <button class="btn-print" onclick="window.print()">
        🖨️ CETAK SERTIFIKAT
    </button>

    <div class="certificate-page">
        <!-- Decoration -->
        <div class="watermark">MySEMS AWARD</div>
        <div class="main-border"></div>
        <div class="inner-border"></div>
        <div class="corner-accent top-left"></div>
        <div class="corner-accent bottom-right"></div>

        <!-- Header -->
        <div class="header">
            <div class="brand-logo" style="display: flex; align-items: center; gap: 0;">
                <img src="{{ asset('img/logo-sertifikat1.png') }}" alt="MySEMS Logo" style="height: 80px; width: auto;">
                <div style="width: 2px; height: 60px; background-color: var(--primary-blue); margin: 0 15px;"></div>
                <div style="text-align: left; color: var(--primary-blue); font-weight: 700; font-size: 14px; line-height: 1.2; max-width: 150px; margin: 0;">
                    Your Personal Partner<br>in Every School Story.
                </div>
            </div>
            <div class="school-logo" style="display: flex; align-items: center; gap: 15px;">
                <img src="{{ asset('img/logo-sekolah.png') }}" alt="School Logo" style="height: 70px; width: auto;">
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <h1 class="title-main">CERTIFICATE</h1>
            <p class="title-sub">OF ACHIEVEMENT</p>
            
            <p class="intro-text">Sertifikat ini dengan bangga diberikan kepada:</p>
            
            <div class="winner-name">{{ $winners['juara1'] }}</div>
            
            <div class="divider"></div>

            <p class="description">
                Atas prestasi dan dedikasi luar biasa dalam memenangkan predikat <strong>JUARA PERTAMA</strong> pada turnamen sekolah resmi:
            </p>
            
            <div class="event-name">{{ $event->nama_event }}</div>
        </div>

        <!-- Footer Signatures -->
        <div class="footer">
            <div class="sig-block">
                <div class="sig-title">Ketua Panitia</div>
                <div class="sig-name">{{ $event->nama_panitia ?: ($event->user->nama ?? 'PANITIA MySEMS') }}</div>
                <div class="sig-subtitle">Penyelenggara Event</div>
            </div>

            <div class="gold-seal"></div>
            
            <div class="sig-block">
                <div class="sig-title">Kepala Sekolah</div>
                <div class="sig-name">Hermanto, S.Pd, M.Pd</div>
                <div class="sig-subtitle">Pengesahan Resmi</div>
            </div>
        </div>
    </div>

</body>
</html>
