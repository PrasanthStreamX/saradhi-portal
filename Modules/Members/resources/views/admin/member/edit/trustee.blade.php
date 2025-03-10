<div class="modal fade" id="editTrustee" tabindex="-1" aria-labelledby="editTrusteeLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form action="{{ route('admin.member.update') }}" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="user_id" value="{{ $member->user->id }}">
            <input type="hidden" name="edit_trustee" value="true">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="editTrusteeLabel">Update Trustee Details</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <div class="col-md-8">
                        <label for="tid" class="form-label">TID <span class="asterisk">*</span></label>
                        <div class="control-col">
                            <input type="text" name="tid" id="tid" value="{{ $member->trustee->tid }}" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-8">
                        <label for="title" class="form-label">Title <span class="asterisk">*</span></label>
                        <div class="control-col">
                            <input type="text" name="title" id="title" value="{{ $member->trustee->title }}" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="joining_date" class="form-label">Joining Date<span class="asterisk">*</span></label>
                        <div class="control-col">
                            <input type="date" name="joining_date" id="joining_date" class="form-control @error('joining_date') is-invalid @enderror" value="{{ $member->trustee->joining_date }}">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-8">
                        <label for="status" class="form-label">Status <span class="asterisk">*</span></label>
                        <div class="control-col">
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="active" @selected( $member->trustee->status == 'active' )>Active</option>
                                <option value="inactive" @selected( $member->trustee->status == 'inactive' )>Inactive</option>
                                <option value="terminated" @selected( $member->trustee->status == 'terminated' )>Terminated</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success" data-bs-dismiss="modal">Save</button>
            </div>
        </form>
      </div>
    </div>
</div>