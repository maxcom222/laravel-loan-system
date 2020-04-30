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
            {{trans_choice('general.expected',1)}} {{trans_choice('general.repayment',2)}}
            @if(!empty($start_date))
                for period: {{$start_date}} to {{$end_date}}
            @endif
        </caption>
        <thead>
        <tr class="">
            <th></th>
            <th>{{trans_choice('general.principal',1)}}</th>
            <th>{{trans_choice('general.interest',1)}}</th>
            <th>{{trans_choice('general.fee',2)}}</th>
            <th>{{trans_choice('general.penalty',2)}}</th>
            <th>{{trans_choice('general.total',1)}}</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><b>{{trans_choice('general.expected',1)}}</b></td>
            <td>{{number_format($due_items["principal"],2)}}</td>
            <td>{{number_format($due_items["interest"],2)}}</td>
            <td>{{number_format($due_items["fees"],2)}}</td>
            <td>{{number_format($due_items["penalty"],2)}}</td>
            <td>{{number_format($due_items["principal"]+$due_items["interest"]+$due_items["fees"]+$due_items["penalty"],2)}}</td>
        </tr>
        <tr>
            <td><b>{{trans_choice('general.actual',1)}}</b></td>
            <td>{{number_format($paid_items["principal"],2)}}</td>
            <td>{{number_format($paid_items["interest"],2)}}</td>
            <td>{{number_format($paid_items["fees"],2)}}</td>
            <td>{{number_format($paid_items["penalty"],2)}}</td>
            <td>{{number_format($paid_items["principal"]+$paid_items["interest"]+$paid_items["fees"]+$paid_items["penalty"],2)}}</td>
        </tr>
        <tr>
            <td><b>{{trans_choice('general.balance',1)}}</b></td>
            <td>{{number_format($due_items["principal"]-$paid_items["principal"],2)}}</td>
            <td>{{number_format($due_items["interest"]-$paid_items["interest"],2)}}</td>
            <td>{{number_format($due_items["fees"]-$paid_items["fees"],2)}}</td>
            <td>{{number_format($due_items["penalty"]-$paid_items["penalty"],2)}}</td>
            <td>{{number_format(($due_items["principal"]+$due_items["interest"]+$due_items["fees"]+$due_items["penalty"])-($paid_items["principal"]+$paid_items["interest"]+$paid_items["fees"]+$paid_items["penalty"]),2)}}</td>
        </tr>
        </tbody>
    </table>
</div>