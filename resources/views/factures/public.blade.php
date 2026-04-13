<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Facture {{ $facture->numero_facture ?? '#'.$facture->id }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Figtree', sans-serif; background: #f3f4f6; color: #374151; margin: 0; padding: 20px; }
        .card { max-width: 800px; margin: 0 auto; background: #fff; border-radius: 12px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden; }
        .card-header { background: #1a1a2e; color: #fff; padding: 28px 32px; display: flex;
                       justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px; }
        .company-block { flex: 1; }
        .company-logo { max-width: 100px; max-height: 60px; margin-bottom: 8px; border-radius: 4px; }
        .company-name { font-size: 18px; font-weight: 700; margin-bottom: 4px; }
        .company-info { font-size: 12px; opacity: 0.8; line-height: 1.6; }
        .invoice-block { text-align: right; }
        .invoice-title { font-size: 26px; font-weight: 700; letter-spacing: 2px; margin-bottom: 6px; }
        .invoice-meta { font-size: 13px; opacity: 0.85; line-height: 1.8; }
        .card-body { padding: 28px 32px; }
        .section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
                         color: #6b7280; margin-bottom: 10px; }
        .client-box { background: #f9fafb; border-radius: 8px; padding: 14px 18px; margin-bottom: 24px; }
        .client-box p { margin: 3px 0; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        thead tr { background: #f3f4f6; }
        thead th { padding: 10px 14px; text-align: left; font-size: 12px; font-weight: 600;
                   text-transform: uppercase; color: #6b7280; border-bottom: 2px solid #e5e7eb; }
        thead th.text-right { text-align: right; }
        tbody td { padding: 10px 14px; font-size: 14px; border-bottom: 1px solid #f3f4f6; }
        tbody td.text-right { text-align: right; }
        .totals-wrapper { display: flex; justify-content: flex-end; margin-bottom: 20px; }
        .totals-box { width: 280px; }
        .totals-row { display: flex; justify-content: space-between; padding: 8px 0;
                      border-bottom: 1px solid #f3f4f6; font-size: 14px; }
        .totals-row.grand-total { background: #1a1a2e; color: #fff; padding: 10px 14px;
                                   border-radius: 8px; margin-top: 4px; font-weight: 700; font-size: 15px; }
        .totals-row.reste { background: #fef3c7; padding: 8px 14px; border-radius: 6px;
                            margin-top: 4px; color: #92400e; font-weight: 600; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px;
                 font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-payee { background: #d1fae5; color: #065f46; }
        .badge-partielle { background: #fef3c7; color: #92400e; }
        .badge-impayee { background: #fee2e2; color: #991b1b; }
        .footer { text-align: center; padding: 16px 32px; background: #f9fafb;
                  border-top: 1px solid #e5e7eb; font-size: 12px; color: #9ca3af; }
    </style>
</head>
<body>

<div class="card">
    <!-- En-tête -->
    <div class="card-header">
        <div class="company-block">
            @if(!empty($entrepriseProfile) && !empty($entrepriseProfile->logo_path))
                <img src="{{ asset('storage/' . $entrepriseProfile->logo_path) }}"
                     alt="Logo" class="company-logo">
            @endif
            @if(!empty($entrepriseProfile))
                <div class="company-name">{{ $entrepriseProfile->nom }}</div>
                <div class="company-info">
                    @if($entrepriseProfile->adresse) {{ $entrepriseProfile->adresse }}<br> @endif
                    @if($entrepriseProfile->telephone) Tél : {{ $entrepriseProfile->telephone }}<br> @endif
                    @if($entrepriseProfile->email) {{ $entrepriseProfile->email }}<br> @endif
                    @if($entrepriseProfile->numero_fiscal) IFU : {{ $entrepriseProfile->numero_fiscal }} @endif
                </div>
            @endif
        </div>
        <div class="invoice-block">
            <div class="invoice-title">FACTURE</div>
            <div class="invoice-meta">
                N° {{ $facture->numero_facture ?? 'FAC-'.$facture->id }}<br>
                Date : {{ \Carbon\Carbon::parse($facture->date)->format('d/m/Y') }}<br>
                @php
                    $badgeClass = match($facture->statut ?? 'impayée') {
                        'payée' => 'badge-payee',
                        'partiellement payée' => 'badge-partielle',
                        default => 'badge-impayee',
                    };
                @endphp
                <span class="badge {{ $badgeClass }}" style="margin-top:6px;display:inline-block;">
                    {{ $facture->statut ?? 'impayée' }}
                </span>
            </div>
        </div>
    </div>

    <div class="card-body">
        <!-- Client -->
        <div class="section-title">Facturé à</div>
        <div class="client-box">
            <p><strong>{{ $facture->client->nom }}</strong></p>
            @if($facture->client->email) <p>{{ $facture->client->email }}</p> @endif
            @if($facture->client->telephone) <p>Tél : {{ $facture->client->telephone }}</p> @endif
            @if($facture->client->adresse) <p>{{ $facture->client->adresse }}</p> @endif
        </div>

        <!-- Produits -->
        <div class="section-title">Détail des prestations</div>
        <table>
            <thead>
                <tr>
                    <th>Désignation</th>
                    <th class="text-right">Qté</th>
                    <th class="text-right">Prix unitaire</th>
                    <th class="text-right">Sous-total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($facture->produits as $produit)
                    <tr>
                        <td>{{ $produit->nom }}</td>
                        <td class="text-right">{{ $produit->pivot->quantite }}</td>
                        <td class="text-right">{{ number_format($produit->pivot->prix, 0, ',', ' ') }} F</td>
                        <td class="text-right">{{ number_format($produit->pivot->prix * $produit->pivot->quantite, 0, ',', ' ') }} F</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totaux -->
        <div class="totals-wrapper">
            <div class="totals-box">
                <div class="totals-row grand-total">
                    <span>Total HT</span>
                    <span>{{ number_format($facture->total, 0, ',', ' ') }} F</span>
                </div>
                <div class="totals-row" style="color:#065f46;">
                    <span>Montant payé</span>
                    <span>{{ number_format($facture->montant_paye, 0, ',', ' ') }} F</span>
                </div>
                @if($facture->reste_a_regler > 0)
                    <div class="totals-row reste">
                        <span>Reste à régler</span>
                        <span>{{ number_format($facture->reste_a_regler, 0, ',', ' ') }} F</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Conditions de paiement -->
        @if(!empty($facture->conditions_paiement))
            <div style="background:#f0f9ff;border-left:3px solid #0ea5e9;padding:12px 16px;border-radius:4px;margin-top:8px;">
                <div class="section-title" style="margin-bottom:6px;">Conditions de paiement</div>
                <p style="font-size:13px;color:#374151;">{{ $facture->conditions_paiement }}</p>
            </div>
        @endif
    </div>

    <div class="footer">
        Document généré le {{ now()->format('d/m/Y') }} — Consultation publique sécurisée
    </div>
</div>

</body>
</html>
