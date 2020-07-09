<div id="modalMsg" class="modal fade " data-backdrop="static">
    <div class="modal-dialog" role="document">
        <form action="">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{config('app.name', 'Production Monitoring System')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <p id="msg_content"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-red" data-dismiss="modal">Close</button>
                </div>
                
            </div>
        </form>
    </div>
</div>