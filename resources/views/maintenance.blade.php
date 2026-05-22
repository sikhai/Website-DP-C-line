<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Website Under Maintenance</title>
    <style>
        @font-face {
            font-family: "DP Céline";
            src: url("{{ asset('fonts/IvyPrestoDisplay-Thin.otf') }}") format("opentype");
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: "PlusJakartaSans-Light";
            src: url("{{ asset('fonts/PlusJakartaSans-Light.ttf') }}") format("truetype");
            font-weight: 300;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: "PlusJakartaSans-Medium";
            src: url("{{ asset('fonts/PlusJakartaSans-Medium.ttf') }}") format("truetype");
            font-weight: 500;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: "PlusJakartaSans-SemiBold";
            src: url("{{ asset('fonts/PlusJakartaSans-SemiBold.ttf') }}") format("truetype");
            font-weight: 600;
            font-style: normal;
            font-display: swap;
        }

        :root {
            --crm-bg: #27384F;
            --crm-accent: #B5A494;
            --ink: #f8f5f1;
            --muted: #d7cfc8;
            --soft-ink: rgba(248, 245, 241, 0.72);
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            color: var(--ink);
            background:
                radial-gradient(circle at 18% 22%, rgba(181, 164, 148, 0.18), transparent 26%),
                linear-gradient(115deg, rgba(39, 56, 79, 0.98), rgba(39, 56, 79, 0.9) 52%, rgba(24, 34, 49, 0.96)),
                url("{{ asset('images/BG_bespoke.png') }}") center / cover no-repeat;
            font-family: "PlusJakartaSans-Medium", Arial, sans-serif;
        }

        body::before {
            position: fixed;
            inset: 0;
            pointer-events: none;
            content: "";
            background:
                linear-gradient(90deg, rgba(255, 255, 255, 0.035) 1px, transparent 1px),
                linear-gradient(0deg, rgba(255, 255, 255, 0.026) 1px, transparent 1px);
            background-size: 92px 92px;
            mask-image: linear-gradient(90deg, transparent, #000 18%, #000 82%, transparent);
        }

        .maintenance-page {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            overflow: hidden;
            padding: clamp(32px, 6vw, 76px);
        }

        .maintenance-page::after {
            position: absolute;
            right: clamp(24px, 6vw, 88px);
            bottom: clamp(24px, 6vw, 76px);
            width: min(30vw, 360px);
            height: 1px;
            content: "";
            background: linear-gradient(90deg, transparent, var(--crm-accent));
            opacity: 0.75;
        }

        .maintenance-panel {
            position: relative;
            z-index: 1;
            width: min(100%, 1080px);
            padding: 0;
            border: 0;
            background: transparent;
            box-shadow: none;
            text-align: center;
        }

        .maintenance-line {
            position: relative;
            display: block;
            width: 1px;
            height: clamp(58px, 10vw, 108px);
            margin: 0 auto 34px;
            background: linear-gradient(180deg, transparent, var(--crm-accent));
        }

        .maintenance-logo {
            width: clamp(70px, 7vw, 94px);
            height: auto;
            margin-bottom: 14px;
            filter: brightness(1.18);
        }

        .maintenance-brand {
            margin: 0 0 clamp(42px, 6vw, 72px);
            color: var(--crm-accent);
            font-size: 14px;
            font-family: "PlusJakartaSans-SemiBold", Arial, sans-serif;
            font-weight: 600;
            letter-spacing: 0.24em;
            text-transform: uppercase;
        }

        .maintenance-eyebrow {
            margin: 0 0 16px;
            color: var(--crm-accent);
            font-family: "PlusJakartaSans-SemiBold", Arial, sans-serif;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.22em;
            text-transform: uppercase;
        }

        h1 {
            margin: 0;
            font-family: "DP Céline", Georgia, "Times New Roman", serif;
            font-size: clamp(44px, 6.3vw, 86px);
            font-weight: 400;
            line-height: 0.98;
            white-space: nowrap;
        }

        .maintenance-copy {
            max-width: 650px;
            margin: 30px auto 0;
            color: var(--soft-ink);
            font-family: "PlusJakartaSans-Light", Arial, sans-serif;
            font-size: clamp(15px, 1.5vw, 18px);
            line-height: 1.8;
        }

        .maintenance-copy::before {
            display: block;
            width: 86px;
            height: 1px;
            margin: 0 auto 28px;
            content: "";
            background: var(--crm-accent);
            opacity: 0.85;
        }

        @media (max-width: 520px) {
            .maintenance-page {
                padding: 28px 20px;
            }

            .maintenance-page::after {
                width: 160px;
            }

            .maintenance-logo {
                margin-bottom: 12px;
            }

            .maintenance-brand {
                margin-bottom: 44px;
            }

            h1 {
                font-size: clamp(40px, 14vw, 58px);
                line-height: 1;
                white-space: normal;
            }
        }

    </style>
</head>

<body>
    <main class="maintenance-page">
        <section class="maintenance-panel" aria-labelledby="maintenance-title">
            <span class="maintenance-line" aria-hidden="true"></span>
            <img class="maintenance-logo" src="{{ asset('images/logo.svg') }}" alt="DPC Line">
            <p class="maintenance-brand">DP CÉLINE</p>
            <p class="maintenance-eyebrow">Maintenance Notice</p>
            <h1 id="maintenance-title">Website Under Maintenance</h1>
            <p class="maintenance-copy">
                We are refining our digital experience with the same attention to detail
                we bring to every space. Please check back soon.
            </p>
        </section>
    </main>
</body>

</html>
