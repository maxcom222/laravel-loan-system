<style>
    table {
        width: 100%;
        border-collapse: collapse;
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
    }

    th, td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #4CAF50;
        color: white;
    }
</style>
<div>

    <table class="">
        <caption>
            {{trans_choice('general.borrower',1)}} {{trans_choice('general.number',2)}}
            @if(!empty($start_date))
                for period: {{$start_date}} to {{$end_date}}
            @endif
        </caption>
        <thead>
        <tr class="">
            <th>{{trans_choice('general.name',1)}}</th>
            <th>{{trans_choice('general.value',1)}}</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $total_borrowers = 0;
        $blacklisted_borrowers = 0;
        $dormant_borrowers = 0;
        $active_borrowers = 0;
        $new_borrowers = 0;
        foreach (\App\Models\Borrower::all() as $key) {
            $total_borrowers = $total_borrowers + 1;
            if ($key->blacklisted == 1) {
                $blacklisted_borrowers = $blacklisted_borrowers + 1;
            }
            if ($start_date <=date_format(date_create($key->created_at),"Y-m-d ")  && $end_date >=date_format(date_create($key->created_at),"Y-m-d ") ) {
                $new_borrowers = $new_borrowers + 1;
            }
            if (count($key->loans) > 0) {
                $active_borrowers = $active_borrowers + 1;
            } else {
                $dormant_borrowers = $dormant_borrowers + 1;
            }
        }
        ?>
        <tr>
            <td>
                {{trans_choice('general.dormant',1)}} {{trans_choice('general.borrower',2)}}
            </td>
            <td>
                {{$dormant_borrowers}}
            </td>
        </tr>
        <tr>
            <td>
                {{trans_choice('general.new',1)}} {{trans_choice('general.borrower',2)}}
            </td>
            <td>
                {{$new_borrowers}}
            </td>
        </tr>
        <tr>
            <td>
                {{trans_choice('general.blacklisted',1)}} {{trans_choice('general.borrower',2)}}
            </td>
            <td>
                {{$blacklisted_borrowers}}
            </td>

        </tr>
        <tr>
            <td>
                {{trans_choice('general.total',1)}} {{trans_choice('general.borrower',2)}}
            </td>
            <td>
                {{$total_borrowers}}
            </td>
        </tr>
        </tbody>

    </table>
</div>