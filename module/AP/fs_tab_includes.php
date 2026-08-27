<?php
// Peta include per tab, 2x2: (fs_system 1/2) x (report_type monthly/ytd).
// Dipakai bareng oleh financial_statement.php (render tab aktif
// server-side) & fs_tab_fetch.php (lazy-load tab lain via AJAX saat
// diklik) - dipisah ke file sendiri supaya kedua tempat itu SELALU sinkron,
// tidak ada risiko satu diupdate tapi yang lain ketinggalan.
return [
    'trial-balance' => [
        '2' => ['ytd' => 'fs_ytd/trial_balance_ytd.php', 'monthly' => 'fs_monthly/trial_balance_monthly.php'],
        '1' => ['ytd' => 'fs1_ytd/trial_balance.php', 'monthly' => 'fs1_monthly/trial_balance.php'],
    ],
    'sfp' => [
        '2' => ['ytd' => 'fs_ytd/statement_financial_position.php', 'monthly' => 'fs_monthly/statement_financial_position_monthly.php'],
        '1' => ['ytd' => 'fs1_ytd/statement_financial_position.php', 'monthly' => 'fs1_monthly/statement_financial_position.php'],
    ],
    'spl' => [
        '2' => ['ytd' => 'fs_ytd/statement_profit_loss.php', 'monthly' => 'fs_monthly/statement_profit_loss_monthly.php'],
        '1' => ['ytd' => 'fs1_ytd/statement_profit_loss.php', 'monthly' => 'fs1_monthly/statement_profit_loss.php'],
    ],
    'cf-direct' => [
        '2' => ['ytd' => 'fs_ytd/cashflow_direct.php', 'monthly' => 'fs_monthly/cashflow_direct_monthly.php'],
        '1' => ['ytd' => 'fs1_ytd/cashflow_direct.php', 'monthly' => 'fs1_monthly/cashflow_direct.php'],
    ],
    'cf-indirect' => [
        '2' => ['ytd' => 'fs_ytd/cashflow_indirect.php', 'monthly' => 'fs_monthly/cashflow_indirect_monthly.php'],
        '1' => ['ytd' => 'fs1_ytd/cashflow_indirect.php', 'monthly' => 'fs1_monthly/cashflow_indirect.php'],
    ],
];
