<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><?php echo e(__('Register')); ?></div>

                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('register')); ?>">
                        <?php echo csrf_field(); ?>

                        <div class="form-group row">
                            <label for="user_id" class="col-md-4 col-form-label text-md-right"><?php echo e(__('User ID')); ?></label>

                            <div class="col-md-6">
                                <input id="user_id" type="text" class="form-control<?php echo e($errors->has('user_id') ? ' is-invalid' : ''); ?>" name="user_id" value="<?php echo e(old('user_id')); ?>" required autofocus>

                                <?php if($errors->has('user_id')): ?>
                                    <span class="invalid-feedback">
                                        <strong><?php echo e($errors->first('user_id')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="firstname" class="col-md-4 col-form-label text-md-right"><?php echo e(__('First Name')); ?></label>

                            <div class="col-md-6">
                                <input id="firstname" type="text" class="form-control<?php echo e($errors->has('firstname') ? ' is-invalid' : ''); ?>" name="firstname" value="<?php echo e(old('firstname')); ?>" required>

                                <?php if($errors->has('firstname')): ?>
                                    <span class="invalid-feedback">
                                        <strong><?php echo e($errors->first('firstname')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="lastname" class="col-md-4 col-form-label text-md-right"><?php echo e(__('Last Name')); ?></label>

                            <div class="col-md-6">
                                <input id="lastname" type="text" class="form-control<?php echo e($errors->has('lastname') ? ' is-invalid' : ''); ?>" name="lastname" value="<?php echo e(old('lastname')); ?>" required>

                                <?php if($errors->has('lastname')): ?>
                                    <span class="invalid-feedback">
                                        <strong><?php echo e($errors->first('lastname')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="department" class="col-md-4 col-form-label text-md-right"><?php echo e(__('Department')); ?></label>
                            <div class="col-md-6">
                                <select id="department" class="form-control<?php echo e($errors->has('department') ? ' is-invalid' : ''); ?>" name="department" required>
                                    <option value="<?php echo e(old('department')); ?>"><?php echo e(old('department')); ?></option>
                                    <option value="PPC">PPC</option>
                                    <option value="Production">Production</option>
                                </select>
                                <?php if($errors->has('department')): ?>
                                    <span class="invalid-feedback">
                                        <strong><?php echo e($errors->first('department')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right"><?php echo e(__('E-Mail Address')); ?></label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control<?php echo e($errors->has('email') ? ' is-invalid' : ''); ?>" name="email" value="<?php echo e(old('email')); ?>" required>

                                <?php if($errors->has('email')): ?>
                                    <span class="invalid-feedback">
                                        <strong><?php echo e($errors->first('email')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right"><?php echo e(__('Password')); ?></label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control<?php echo e($errors->has('password') ? ' is-invalid' : ''); ?>" name="password" required>

                                <?php if($errors->has('password')): ?>
                                    <span class="invalid-feedback">
                                        <strong><?php echo e($errors->first('password')); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right"><?php echo e(__('Confirm Password')); ?></label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    <?php echo e(__('Register')); ?>

                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>