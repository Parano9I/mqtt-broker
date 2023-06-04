<template>
  <div class="sign-in">
    <a-row type="flex" :gutter="[24, 24]" justify="space-around" align="middle">
      <a-col
        :span="24"
        :md="12"
        :lg="{ span: 12, offset: 0 }"
        :xl="{ span: 6, offset: 2 }"
        class="col-form"
      >
        <h1 class="mb-15">Sign Up</h1>

        <a-form
          id="components-form-demo-normal-login"
          :form="form"
          class="login-form"
          @submit="handleSubmit"
          :hideRequiredMark="true"
        >
          <a-form-item class="mb-10" label="Login" :colon="false">
            <a-input
              v-decorator="[
                'login',
                {
                  rules: [
                    { required: true, message: 'Please input your login!' },
                  ],
                },
              ]"
              placeholder="Login"
            />
          </a-form-item>
          <a-form-item class="mb-10" label="Email" :colon="false">
            <a-input
              v-decorator="[
                'email',
                {
                  rules: [
                    { required: true, message: 'Please input your email!' },
                  ],
                },
              ]"
              placeholder="Email"
            />
          </a-form-item>
          <a-form-item class="mb-5" label="Password" :colon="false">
            <a-input
              v-decorator="[
                'password',
                {
                  rules: [
                    { required: true, message: 'Please input your password!' },
                  ],
                },
              ]"
              type="password"
              placeholder="Password"
            />
          </a-form-item>
          <a-form-item class="mb-5" label="Confirm Password" :colon="false">
            <a-input
              v-decorator="[
                'password_confirmation',
                {
                  rules: [
                    {
                      required: true,
                      message: 'Please input confirm password!',
                    },
                    {
                      validator: this.validateConfirmPassword,
                    },
                  ],
                },
              ]"
              type="password"
              placeholder="Confirm Password"
            />
          </a-form-item>
          <a-form-item>
            <a-button
              type="primary"
              block
              html-type="submit"
              class="login-form-button"
            >
              SIGN IN
            </a-button>
          </a-form-item>
        </a-form>

        <p class="font-semibold text-muted">
          Don't have an account?
          <router-link to="/auth/sign-in" class="font-bold text-dark"
            >Sign In
          </router-link>
        </p>
      </a-col>

      <a-col :span="24" :md="12" :lg="12" :xl="12" class="col-img">
        <img src="/images/img-signin.jpg" alt="" />
      </a-col>
    </a-row>
  </div>
</template>

<script>
import { usersApi } from "@/services/http/api/users";
import { notification } from "ant-design-vue";

export default {
  data() {
    return {};
  },
  beforeCreate() {
    this.form = this.$form.createForm(this, { name: "normal_login" });
  },
  methods: {
    handleSubmit(e) {
      e.preventDefault();
      this.form.validateFields(async (err, values) => {
        if (!err) {
          const [error, data] = await usersApi.create(values);

          console.log(error, data);

          if (error) {
            switch (error.status) {
              case 422: {
                const errors = error.data.errors;
                Object.keys(errors).forEach((key) => {
                  this.form.setFields({
                    [key]: {
                      errors: [new Error(errors[key][0])],
                    },
                  });
                });
              }
            }
          } else {
            notification.success({
              message: `User register success !`,
              description: "Success",
              duration: 3,
            });
            this.$router.push("/auth/sign-in");
          }
        }
      });
    },
    validateConfirmPassword(rule, value, callback) {
      if (value && value !== this.form.getFieldValue("password")) {
        callback("Passwords do not match");
      } else {
        callback();
      }
    },
  },
};
</script>

<style lang="scss">
body {
  background-color: #ffffff;
}
</style>
