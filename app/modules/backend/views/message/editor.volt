<div class="modal fade" id="editor-modal" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">
                   修改消息信息
                </h4>
            </div>
            <div class="modal-body">
                {{ form('admin/advertise/update', 'id':'editor-modal-form', 'role': 'form', 'enctype':'multipart/form-data') }}
                <input class="form-control" type="hidden" name="id-editor-modal" id="id-editor-modal"/>
                <div class="form-group">
                    <label>消息标题</label>
                    <input class="form-control" type="text" name="title-editor-modal" id="title-editor-modal"/>
                </div>
                <div class="form-group">
                    <label>消息链接</label>
                    <input class="form-control" type="text" name="link-editor-modal" id="link-editor-modal"/>
                </div>
                <div class="form-group">
                    <label>消息描述</label>
                    <input class="form-control" type="text" name="description-editor-modal" id="description-editor-modal"/>
                </div>
                <div class="form-group">
                    <label>其他说明</label>
                    <input class="form-control" type="text" name="memo-editor-modal" id="memo-editor-modal"/>
                </div>
                <div class="form-group">
                    <label>图片</label>
                    <input type="file" id="image-editor-modal" name="image-editor-modal" class="form-control" />
                </div>

                {{ submit_button('提交', 'class': 'btn btn-primary') }}
                <button class="btn btn-default pull-right"  data-dismiss="modal">取消</button>
                </form>

            </div>

            <div class="modal-footer">
                {#<button type="button" class="btn btn-default"#}
                        {#data-dismiss="modal">关闭#}
                {#</button>#}
                {#<button type="button" class="btn btn-primary">#}
                    {#提交更改#}
                {#</button>#}
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>
