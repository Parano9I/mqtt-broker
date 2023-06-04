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
        <h1 class="mb-15">Sign In</h1>
        <h5 class="font-regular text-muted">
          Enter your email and password to sign in
        </h5>

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
          <router-link to="/auth/sign-up" class="font-bold text-dark"
            >Sign Up
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
import { notification } from "ant-design-vue";
import { authAPI } from "@/services/http/api/auth";
import { useUserStore } from "@/store/user";

export default {
  data() {
    return {
      userStore: useUserStore(),
    };
  },
  beforeCreate() {
    this.form = this.$form.createForm(this, { name: "normal_login" });
  },
  methods: {
    handleSubmit(e) {
      e.preventDefault();
      this.form.validateFields(async (err, values) => {
        if (!err) {
          const [error, response] = await authAPI.login(values);

          if (error) {
            console.log(error, response);
            switch (error.status) {
              case 422: {
                const errors = error.data.errors;
                Object.keys(errors).forEach((key) => {
                  console.log(key);
                  this.form.setFields({
                    [key]: {
                      errors: [new Error(errors[key][0])],
                    },
                  });
                });
                break;
              }
              case 401: {
                const errors = error.data.errors;
                notification.error({
                  message: errors.title,
                  description: errors.detail,
                  duration: 5,
                });
                break;
              }
              default:
                notification.error({
                  message: "Error !",
                  description: error.message,
                  duration: 3,
                });
                break;
            }
          } else {
            const data = response.data;
            this.userStore.setUser({
              id: data.user.id,
              ...data.user.attributes,
            });
            this.userStore.setToken(data.token.attributes.payload);

            await this.$router.push("/");
          }
        }
      });
    },
  },
};
</script>

<style lang="scss">
body {
  background-color: #ffffff;
}
</style>
