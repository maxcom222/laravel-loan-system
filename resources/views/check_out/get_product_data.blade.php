<?php $id = uniqid() ?>
<tr id="{{$id}}">
    <input type="hidden" class="form-control" value="{{$product->id}}" name="product_id[]"/>
    <input type="hidden" class="form-control" value="{{$product->name}}" name="" disabled=""/>
    <input type="hidden" class="form-control" value="{{$product->cost_price}}" name="cost_price[]" disabled=""/>
    <td>
        {{$product->name}}
    </td>
    <td>
        <input type="number" class="form-control" value="1" name="qty[]" required id="qty_{{$id}}" onblur="doCalc()"/>
    </td>
    <td>
        {{$product->cost_price}}
    </td>
    <td>
        <span class="amount">{{$product->cost_price}}</span>
    </td>
    <td>
        <button type="button" class="btn btn-danger btn-xs delete" onclick="deleteRow(this);" data-id="{{$id}}"><i class="fa fa-trash"></i></button>
    </td>
</tr>