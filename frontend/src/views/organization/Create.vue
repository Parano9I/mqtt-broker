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
        <h1 class="mb-15">Create Organization</h1>

        <a-form
          id="components-form-demo-normal-organization"
          :form="form"
          class="organization-form"
          @submit="handleSubmit"
          :hideRequiredMark="true"
        >
          <a-form-item class="mb-10" label="Name" :colon="false">
            <a-input
              v-decorator="[
                'name',
                {
                  rules: [
                    {
                      required: true,
                      message: 'Please input organization name!',
                    },
                  ],
                },
              ]"
              placeholder="Name"
            />
          </a-form-item>
          <a-form-item class="mb-10" label="Description" :colon="false">
            <a-input
              type="textarea"
              v-decorator="[
                'description',
                {
                  rules: [
                    {
                      required: true,
                      message: 'Please input organization description!',
                    },
                  ],
                },
              ]"
              placeholder="Description"
            />
          </a-form-item>
          <a-form-item>
            <a-button
              type="primary"
              block
              html-type="submit"
              class="organization-form-button"
            >
              CREATE
            </a-button>
          </a-form-item>
        </a-form>
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
import { organizationAPI } from "@/services/http/api/organization";
import { useOrganizationStore } from "@/store/organization";

export default {
  name: "Create",
  data() {
    return {
      organizationStore: useOrganizationStore(),
    };
  },
  beforeCreate() {
    this.form = this.$form.createForm(this, { name: "normal-organization" });
  },
  methods: {
    handleSubmit(e) {
      e.preventDefault();
      this.form.validateFields(async (err, values) => {
        if (!err) {
          const [error, data] = await organizationAPI.create(values);

          if (error) {
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
              }
            }
          } else {
            this.organizationStore.setOrganization({
              id: data.data.id,
              ...data.data.attributes,
            });
            await this.$router.push("/auth/sign-up");
          }
        }
      });
    },
  },
};
</script>

<style lang="scss">
body {
  background-color: #ffffff !important;
}
</style>
