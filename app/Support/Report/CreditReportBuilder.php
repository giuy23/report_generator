<?php

namespace App\Support\Report;

use Illuminate\Support\Facades\DB;

class CreditReportBuilder
{
    public static function build(string $start_date, string $end_date)
    {
        $report_loan = DB::table('subscription_reports as sr')
            ->join('subscriptions as s', 's.id', '=', 'sr.subscription_id')
            ->join('report_loans as l', 'l.subscription_report_id', '=', 'sr.id')
            ->selectRaw("
                sr.id,
                s.full_name,
                s.document,
                s.email,
                s.phone,
                l.bank as company,
                'LOAN' as debt_type,
                l.status as situation,
                l.expiration_days as delay,
                l.bank as entity,
                l.amount as total_amount,
                NULL as total_line,
                NULL as used_line,
                sr.created_at as report_date,
                'ACTIVE' as status
            ");

        $report_credit = DB::table('subscription_reports as sr')
            ->join('subscriptions as s', 's.id', '=', 'sr.subscription_id')
            ->join('report_credit_cards as c', 'c.subscription_report_id', '=', 'sr.id')
            ->selectRaw("
                sr.id,
                s.full_name,
                s.document,
                s.email,
                s.phone,
                c.bank as company,
                'CREDIT_CARD',
                'NOR',
                0,
                c.bank,
                NULL,
                c.line,
                c.used,
                sr.created_at,
                'ACTIVE'
            ");

        $report_other_debts = DB::table('subscription_reports as sr')
            ->join('subscriptions as s', 's.id', '=', 'sr.subscription_id')
            ->join('report_other_debts as o', 'o.subscription_report_id', '=', 'sr.id')
            ->selectRaw("
                sr.id,
                s.full_name,
                s.document,
                s.email,
                s.phone,
                o.entity,
                'OTHER',
                'NOR',
                o.expiration_days,
                o.entity,
                o.amount,
                NULL,
                NULL,
                sr.created_at,
                'ACTIVE'
            ");

        return DB::query()
            ->fromSub(
                $report_loan->unionAll($report_credit)->unionAll($report_other_debts),
                'report_subcriptions'
            )
            ->whereBetween('report_date', [$start_date, $end_date])
            ->orderBy('report_date');
    }
}
