<div class="modal fade" id="myModal" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">
                   修改广告信息
                </h4>
            </div>
            <div class="modal-body">
                {{ form('admin/advertise/update', 'role': 'form', 'enctype':'multipart/form-data') }}
                <div class="form-group">
                    <label>广告标题</label>
                    <input class="form-control" type="text" name="title-modal" id="title-modal"/>
                </div>
                <div class="form-group">
                    <label>广告链接</label>
                    <input class="form-control" type="text" name="link-modal" id="link-modal"/>
                </div>
                <div class="form-group">
                    <label>广告描述</label>
                    <input class="form-control" type="text" name="description-modal" id="description-modal"/>
                </div>
                <div class="form-group">
                    <label>其他说明</label>
                    <input class="form-control" type="text" name="memo-modal" id="memo-modal"/>
                </div>
                <div class="form-group">
                    <label>图片</label>
                    <img src="http://linkage.b0.upaiyun.com/image/2016/03/13/7a03f6ac22620c78d28c4328ff404578.png"  alt="上海鲜花港 - 郁金香" />
                </div>

                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal">关闭
                </button>
                <button type="button" class="btn btn-primary">
                    提交更改
                </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>