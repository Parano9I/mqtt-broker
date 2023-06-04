<template>
  <a-form
    id="components-form-demo-normal-create"
    :form="form"
    class="login-form"
    @submit="handleSubmit"
    :hideRequiredMark="true"
  >
    <a-form-item class="mb-10" label="Name" :colon="false">
      <a-input
        v-decorator="[
          'name',
          {
            rules: [{ required: true, message: 'Please input sensor name!' }],
          },
        ]"
        placeholder="Title"
      />
    </a-form-item>
    <a-form-item class="mb-5" label="Description" :colon="false">
      <a-input
        v-decorator="[
          'description',
          {
            rules: [
              { required: true, message: 'Please input sensor description!' },
            ],
          },
        ]"
        placeholder="Description"
      />
    </a-form-item>
    <a-form-item class="mb-5" label="Topic" :colon="false">
      <a-tree-select
        @change="handleChange"
        :tree-data="topics"
        :replaceFields="treeFields"
        tree-default-expand-all
        v-decorator="[
          'tree-select',
          {
            initialValue: selectedTopic,
            rules: [{ required: true, message: 'Please select sensor topic!' }],
          },
        ]"
      ></a-tree-select>
    </a-form-item>
    <a-form-item>
      <a-button
        type="primary"
        block
        html-type="submit"
        class="login-form-button"
      >
        Create
      </a-button>
    </a-form-item>
  </a-form>
</template>

<script>
import { topicsAPI } from "@/services/http/api/topics";
import { sensorsAPI } from "@/services/http/api/sensors";
import { notification } from "ant-design-vue";

export default {
  name: "CreateSensorForm",
  data() {
    return {
      topics: [],
      selectedTopic: null,
      treeFields: {
        title: "name",
        key: "id",
        value: "id",
        pId: "parent_id",
      },
    };
  },
  mounted() {
    this.getTopics();
  },
  beforeCreate() {
    this.form = this.$form.createForm(this, { name: "normal_create" });
  },
  methods: {
    handleChange(value) {
      this.selectedTopic = value;
    },
    async getTopics() {
      const [error, response] = await topicsAPI.getAllByGroup(
        this.$route.params.id
      );

      if (error) {
        console.log(error.message);
      } else {
        this.topics = response.data;
      }
    },
    async handleSubmit(e) {
      e.preventDefault();
      this.form.validateFields(async (err, values) => {
        if (!err) {
          const params = {
            group: this.$route.params.id,
            topic: this.selectedTopic,
          };

          const [error, response] = await sensorsAPI.create(params, values);

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
            this.$emit("onAfterSubmit", response);
          }
        }
      });
    },
  },
};
</script>

<style scoped></style>
