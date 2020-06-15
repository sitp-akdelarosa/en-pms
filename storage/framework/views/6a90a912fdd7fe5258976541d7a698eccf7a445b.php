<?php $__env->startSection('content'); ?>
<form action="<?php echo e(route('login')); ?>" method="post" class="form-element">
    <?php echo csrf_field(); ?>
    <div class="form-group has-feedback">
        <input type="text" class="form-control<?php echo e($errors->has('user_id') ? ' is-invalid' : ''); ?>" name="user_id" placeholder="User ID" value="<?php echo e(old('user_id')); ?>" required autofocus>
        <span class="ion ion-person form-control-feedback"></span>
        <?php if($errors->has('user_id')): ?>
            <span class="invalid-feedback">
                <strong><?php echo e($errors->first('user_id')); ?></strong>
            </span>
        <?php endif; ?>
    </div>

    <div class="form-group has-feedback">
        <input type="password" class="form-control<?php echo e($errors->has('password') ? ' is-invalid' : ''); ?>" placeholder="Password" name="password" required>
        <span class="ion ion-locked form-control-feedback"></span>
        <?php if($errors->has('password')): ?>
            <span class="invalid-feedback">
                <strong><?php echo e($errors->first('password')); ?></strong>
            </span>
        <?php endif; ?>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="checkbox">
                <label><input type="checkbox" name="remember" <?php echo e(old('remember') ? 'checked' : ''); ?>>
                Remember Me</label>
            </div>
        </div>
        <div class="col-6">
            <button type="submit" class="btn btn-info btn-block btn-flat margin-top-10">LOGIN</button>
        </div>

        <div class="col-6">
            <button type="button" class="btn btn-info btn-block btn-flat margin-top-10">RESET</button>
        </div>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>