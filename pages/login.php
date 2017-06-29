<div class="row">

    <div class="col-sm-6 offset-sm-3">

        <form method="post" action="<?php echo LOGIN; ?>">

            <div class="form-group row">
                <label for="username" class="col-sm-3 col-form-label">Username</label>
                <div class="col-sm-9">
                    <input class="form-control" type="text" name="username" autofocus>
                </div>
            </div>

            <div class="form-group row">
                <label for="password" class="col-sm-3 col-form-label">Password</label>
                <div class="col-sm-9">
                    <input class="form-control" type="password" name="password">
                </div>
            </div>

            <div class="form-group row mb-0">
                <div class="offset-sm-3 col-sm-9">
                    <button type="submit" class="btn btn-primary">Sign in</button>
                </div>
            </div>

        </form>

    </div>

</div>
