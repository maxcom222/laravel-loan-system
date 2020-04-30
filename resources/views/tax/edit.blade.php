<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">×</span></button>
    <h4 class="modal-title">Edit Tax</h4>
</div>
{!! Form::open(array('url' => url('tax/'.$tax->id.'/update'),'method'=>'post')) !!}
<div class="modal-body">
    <div class="row">
        <div class="col-md-12" style="">

            <div class="form-group">
                <div class="form-line">
                    {!!  Form::label( 'Name',null,array('class'=>' control-label')) !!}
                    {!! Form::text('name',$tax->title,array('class'=>'form-control ','required'=>'required')) !!}
                </div>
            </div>
            <div class="form-group">
                <div class="form-line">
                    {!!  Form::label( 'Percentage',null,array('class'=>' control-label')) !!}
                    {!! Form::text('percentage',$tax->percentage,array('class'=>'form-control touchspin','required'=>'required')) !!}
                </div>
            </div>
            <div class="form-group">
                <div class="form-line">
                    {!!  Form::label( 'Notes',null,array('class'=>' control-label')) !!}
                    {!! Form::textarea('notes',$tax->notes,array('class'=>'form-control')) !!}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-info">Save</button>
    <button type="button" class="btn default" data-dismiss="modal">Close</button>
</div>
{!! Form::close() !!}