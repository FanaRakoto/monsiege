<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Billet MonSiège {{ $ticket->ticket_number }}</title>
    <style>
        @page { margin: 22px 24px 26px; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: DejaVu Sans, sans-serif; color: #183329; font-size: 8.4px; line-height: 1.28; }
        table { width: 100%; border-collapse: collapse; }
        .top td { border: 0; padding: 0; vertical-align: middle; }
        .logo { width: 46px; height: 46px; object-fit: contain; }
        .brand-name { color: #0b6e4f; font-size: 18px; font-weight: bold; letter-spacing: -.4px; }
        .brand-sub { color: #6b7c74; font-size: 7px; letter-spacing: 1.2px; text-transform: uppercase; }
        .ticket-ref { text-align: right; }
        .ticket-no { color: #0b6e4f; font-size: 14px; font-weight: bold; }
        .status { display: inline-block; margin-top: 4px; padding: 4px 7px; border-radius: 10px; background: #e7f5ed; color: #07583f; font-size: 7px; font-weight: bold; }
        .status.used { background: #edf0ef; color: #53635c; }
        .status.cancelled { background: #fff0f0; color: #a32b37; }
        .route { margin-top: 10px; border: 1px solid #d7e7df; background: #f3f8f5; border-radius: 8px; }
        .route td { width: 45%; border: 0; padding: 9px 11px; vertical-align: middle; }
        .route .arrow { width: 10%; text-align: center; color: #f47c20; font-size: 17px; font-weight: bold; }
        .label { color: #718078; font-size: 6.8px; text-transform: uppercase; letter-spacing: .7px; }
        .city { margin-top: 3px; color: #123b2c; font-size: 13px; font-weight: bold; }
        .time { margin-top: 2px; color: #0b6e4f; font-size: 17px; font-weight: bold; }
        .address { margin-top: 2px; color: #65756d; font-size: 7px; }
        .section { margin: 9px 0 4px; color: #0b6e4f; font-size: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: .7px; }
        .details td { border: 1px solid #e2ebe6; padding: 5px 6px; vertical-align: middle; }
        .details .key { width: 17%; color: #6a7a72; background: #f8faf9; font-size: 6.7px; text-transform: uppercase; }
        .details .val { width: 33%; color: #1b352a; font-weight: bold; }
        .seats { margin-top: 2px; }
        .seats th { padding: 5px 6px; color: #fff; background: #0b6e4f; text-align: left; font-size: 6.8px; text-transform: uppercase; letter-spacing: .4px; }
        .seats td { padding: 5px 6px; border: 1px solid #e6eee9; color: #243b31; font-size: 8px; }
        .seat-chip { display: inline-block; margin: 2px 3px 2px 0; padding: 4px 7px; border: 1px solid #cfe3d8; border-radius: 5px; background: #f2f8f4; color: #0b6e4f; font-weight: bold; }
        .summary td { padding: 6px 7px; border: 1px solid #dfe9e3; background: #f6faf7; vertical-align: middle; }
        .summary .amount { color: #0b6e4f; font-size: 13px; font-weight: bold; }
        .qr-area { margin-top: 8px; border: 1px solid #dce8e1; border-radius: 8px; padding: 7px; }
        .qr-area td { border: 0; padding: 3px; vertical-align: middle; }
        .qr { width: 91px; height: 91px; }
        .note { color: #64756d; font-size: 7px; line-height: 1.35; }
        .footer { margin-top: 7px; padding-top: 5px; border-top: 1px solid #e1e9e4; color: #78877f; font-size: 6.5px; text-align: center; }
    </style>
</head>
<body>
    <table class="top">
        <tr>
            <td style="width:58px;">
                @if(!empty($logoData))
                    <img class="logo" src="{{ $logoData }}" alt="MonSiège">
                @else
                    <div style="width:43px;height:43px;line-height:43px;text-align:center;background:#0b6e4f;color:#fff;border-radius:9px;font-size:22px;font-weight:bold;">M</div>
                @endif
            </td>
            <td>
                <div class="brand-name">MonSiège</div>
                <div class="brand-sub">Transport interurbain · Madagascar</div>
                <div style="margin-top:4px;color:#697a71;font-size:7px;">Billet de groupe · À présenter à l'embarquement</div>
            </td>
            <td class="ticket-ref" style="width:34%;">
                <div class="label">Numéro du billet unique</div>
                <div class="ticket-no">{{ $ticket->ticket_number }}</div>
                <span class="status {{ $ticket->status === 'used' ? 'used' : ($ticket->status === 'valid' ? '' : 'cancelled') }}">
                    {{ $ticket->status === 'valid' ? 'BILLET VALIDE' : ($ticket->status === 'used' ? 'DÉJÀ UTILISÉ' : 'BILLET ANNULÉ') }}
                </span>
            </td>
        </tr>
    </table>

    <table class="route">
        <tr>
            <td>
                <div class="label">Départ · {{ $trip->departure_at?->format('d/m/Y') ?? 'Date non renseignée' }}</div>
                <div class="city">{{ $trip->departure_city }}</div>
                <div class="time">{{ $trip->departure_at?->format('H:i') ?? '--:--' }}</div>
                @if($trip->departure_address)<div class="address">{{ $trip->departure_address }}</div>@endif
            </td>
            <td class="arrow">&#8594;</td>
            <td>
                <div class="label">Arrivée · {{ $trip->arrival_at?->format('d/m/Y') ?? 'Date non renseignée' }}</div>
                <div class="city">{{ $trip->arrival_city }}</div>
                <div class="time">{{ $trip->arrival_at?->format('H:i') ?? '--:--' }}</div>
                @if($trip->arrival_address)<div class="address">{{ $trip->arrival_address }}</div>@endif
            </td>
        </tr>
    </table>

    <div class="section">Véhicule et équipe</div>
    <table class="details">
        <tr>
            <td class="key">Véhicule</td>
            <td class="val">{{ trim(($vehicle?->brand ?? '') . ' ' . ($vehicle?->model ?? '')) ?: 'Non renseigné' }}</td>
            <td class="key">Type</td>
            <td class="val">{{ $vehicle?->type ? ucfirst(str_replace('_', ' ', $vehicle->type)) : 'Non renseigné' }}</td>
        </tr>
        <tr>
            <td class="key">Immatriculation</td>
            <td class="val">{{ $vehicle?->registration_number ?? 'Non renseignée' }}</td>
            <td class="key">Capacité véhicule</td>
            <td class="val">{{ $vehicle?->capacity ? $vehicle->capacity . ' places' : 'Non renseignée' }}</td>
        </tr>
        <tr>
            <td class="key">Chauffeur</td>
            <td class="val">{{ $driver?->name ?? 'Non affecté' }}</td>
            <td class="key">Receveur</td>
            <td class="val">{{ filled($receiverName) ? $receiverName : 'Non renseigné' }}</td>
        </tr>
        <tr>
            <td class="key">Agence</td>
            <td class="val" colspan="3">{{ $agency?->name ?? 'Agence non renseignée' }}</td>
        </tr>
    </table>

    <div class="section">Responsable de la réservation</div>
    <table class="details">
        <tr>
            <td class="key">Nom complet</td>
            <td class="val">{{ $contact ? trim($contact->first_name . ' ' . $contact->last_name) : 'Non renseigné' }}</td>
            <td class="key">Téléphone</td>
            <td class="val">{{ $contact?->phone ?: '—' }}</td>
        </tr>
        <tr>
            <td class="key">E-mail</td>
            <td class="val" colspan="3">{{ $contact?->email ?: 'Non renseigné' }}</td>
        </tr>
    </table>

    <div class="section">Groupe et sièges couverts par ce billet</div>
    <table class="seats">
        <thead><tr><th style="width:23%;">Nombre de personnes</th><th>Sièges réservés</th></tr></thead>
        <tbody>
            <tr>
                <td><strong style="font-size:13px;color:#0b6e4f;">{{ $reservation->passenger_count }}</strong> personne(s)</td>
                <td>
                    @forelse($seatNumbers as $seatNumber)
                        <span class="seat-chip">{{ $seatNumber }}</span>
                    @empty
                        —
                    @endforelse
                </td>
            </tr>
        </tbody>
    </table>

    <div class="section">Résumé de réservation</div>
    <table class="summary">
        <tr>
            <td style="width:28%;"><div class="label">Code réservation</div><strong>{{ $reservation->reservation_code ?? '—' }}</strong></td>
            <td style="width:18%;"><div class="label">Billet</div><strong>1 billet de groupe</strong></td>
            <td style="width:27%;"><div class="label">Prix par siège</div><strong>{{ number_format((float) $trip->price, 0, ',', ' ') }} Ar</strong></td>
            <td style="width:27%;"><div class="label">Total réservé</div><div class="amount">{{ number_format((float) $reservation->total_amount, 0, ',', ' ') }} Ar</div></td>
        </tr>
    </table>

    <table class="qr-area">
        <tr>
            <td style="width:72%;">
                <strong style="font-size:8px;color:#0b6e4f;">Contrôle à l'embarquement</strong>
                <p class="note" style="margin:4px 0;">Ce billet unique couvre le groupe et tous les sièges listés ci-dessus. Présentez le QR Code au chauffeur ou au personnel autorisé. Chaque personne doit occuper un siège réservé.</p>
                <p class="note" style="margin:4px 0 0;">Billet émis le {{ $issuedAtDisplay }}. Le QR Code vérifie la réservation complète et ne contient pas les coordonnées personnelles.</p>
            </td>
            <td style="width:28%;text-align:center;">
                <img class="qr" src="{{ $qrCode }}" alt="QR Code du billet de groupe">
                <div style="margin-top:2px;color:#0b6e4f;font-size:7px;font-weight:bold;">QR CODE DU GROUPE</div>
            </td>
        </tr>
    </table>

    <div class="footer">MonSiège · Le tarif est calculé par siège réservé. La simulation locale n'est pas un encaissement bancaire réel.</div>
</body>
</html>