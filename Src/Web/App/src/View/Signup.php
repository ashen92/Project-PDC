<div class="grid justify-content-center margin-top-60px">
    <div class="text-subtitle">Create an account</div>
    <div id="signin-area" class="box">
        <form action="" method="post">
            <div class="form-field">
                <label for="firstname">First name</label>
                <input type="text" name="firstname" id="firstname" required>
            </div>
            <div class="form-field">
                <label for="lastname">Last name</label>
                <input type="text" name="lastname" id="lastname" required>
            </div>
            <div class="form-field">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-field">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="form-field">
                <label for="repassword">Re-enter Password</label>
                <input type="password" name="repassword" id="repassword" required>
            </div>
            <div class="form-field submit">
                <input type="submit" value="Create">
            </div>
        </form>
        <div class="padding-y-15px">Already have an account? <a href="/signin">Sign in</a></div>
    </div>
</div>