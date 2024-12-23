@extends('layouts.admin')


@section('content')
<div class="page-title">
    <div class="title-container">
        <h1 class="title">Create Committee</h1>
    </div>
    <div class="actions">
        
    </div>
</div>
<div class="page-content">
    <div class="row">
        <div class="col-md-6">
            <div class="form-container">
                <form action="{{ route('admin.committee.create') }}" method="POST">
                    @csrf
                    <div class="form-group row">
                        <div class="col">
                            <label for="committee_type_id" class="form-label">Committee Type</label>
                            <select name="committee_type_id" id="committee_type_id" class="form-select">
                                <option value="">Select</option>
                                @foreach ($committee_types as $committee_type)
                                    <option value="{{$committee_type->id}}" data-category="{{$committee_type->category}}">{{$committee_type->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <div id="unitContainer">
                                <label for="member_unit_id" class="form-label">Unit</label>
                                <select name="member_unit_id" id="member_unit_id" class="form-select">
                                    <option value="">Select</option>
                                    @foreach ($units as $unit)
                                        <option value="{{$unit->id}}">{{$unit->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row" id="item">
                        <div class="col-md-4">
                            <label for="formed_on" class="form-label">Starting from</label>
                            <input type="date" name="formed_on" id="formed_on" class="form-control">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('page_scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>
<script>
    $(document).ready(function(){
        $('#unitContainer').hide();
        $('#committee_type').on('change', function(){
            var category = $(this).find(':selected').data('category');
            showUnit(category);
        });

        var designation_title = $('#designation_title').find(':selected').data('title');
        var designation_id = $('#designation_title').find(':selected').val();
        toggleSearchInput(designation_id);
        $('#designation_title').on('change', function(){
            designation_title = $(this).find(':selected').data('title');
            designation_id = $(this).find(':selected').val();
            $('#search').prop('disabled', false);
            toggleSearchInput(designation_id);
        });

        var path = "{{ route('admin.committee.autocomplete') }}";   
        $('#search').typeahead( {
            name: 'best-pictures',
            displayKey: 'value',
            source: function (query, process) {
                return $.get(path, {
                    query: query
                }, function (data) {
                    return process(data);
                })
            },
            updater: function (item) {
                $('#typeHeadResult').append(
                    '<tr id="thrRow'+item.user.id+'">'
                    +'<td><input type="hidden" name="designation[]" class="form-control" value="'+designation_id+'">'+designation_title+'</td>'
                    +'<td><input type="hidden" name="members[]" value="'+item.user.id+'">'
                        +'<div class="profile-pill"><div class="details"><div class="title">'+item.user.name+'</div><div>'+item.user.email+'</div><div>MID: '+item.membership.mid+'</div></div></div>'
                    +'</td>' 
                    +'<td><a class="btn btn-xs btn-outline-danger btn-remove-typeHead" data-id="'+item.user.id+'">Remove</a></td>'
                    +'</tr>'
                );       
            }
        });
        $(document).on('click', '.btn-remove-typeHead',function(){
            var id = $(this).data("id");
            $(this).closest('#thrRow'+id).remove();
        });
    });

    function toggleSearchInput(handle){
        if(handle == ''){
            $('#search').prop('disabled', true);
        }
    }

    function showUnit(category){
        if(category == 'unit'){
            $('#unitContainer').show();
        }else{
            $('#unitContainer').hide();
        }
    }

    
</script>
@endsection