<template>
  <a-form
    id="components-form-demo-normal-create"
    :form="form"
    class="login-form"
    @submit="handleSubmit"
    :hideRequiredMark="true"
  >
    <a-form-item class="mb-5" label="Parent topic" :colon="false">
      <a-tree-select
        @change="handleChange"
        :tree-data="topics"
        :replaceFields="treeFields"
        tree-default-expand-all
      ></a-tree-select>
    </a-form-item>
    <a-form-item class="mb-10" label="Name" :colon="false">
      <a-input
        v-decorator="[
          'name',
          {
            rules: [{ required: true, message: 'Please input group name!' }],
          },
        ]"
        placeholder="Name"
      />
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
import { groupsAPI } from "@/services/http/api/groups";
import { notification } from "ant-design-vue";
import { topicsAPI } from "@/services/http/api/topics";

export default {
  name: "CreateTopicForm",
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
  beforeCreate() {
    this.form = this.$form.createForm(this, { name: "normal_create" });
  },
  mounted() {
    this.getTopics();
  },
  methods: {
    handleChange(value) {
      this.selectedTopic = value;
    },
    handleSubmit(e) {
      e.preventDefault();
      this.form.validateFields(async (err, values) => {
        if (!err) {
          const [error, response] = await topicsAPI.create(
            {
              group: this.$route.params.id,
            },
            {
              parent_id: this.selectedTopic,
              ...values,
            }
          );

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
            this.$emit("onAfterSubmit", response);
          }
        }
      });
    },
    async getTopics() {
      const [error, response] = await topicsAPI.getAllByGroup(
        this.$route.params.id
      );

      if (error) {
        console.log(error.message);
      } else {
        this.topics = [
          {
            id: null,
            name: "Without parent topic",
          },
          ...response.data,
        ];
        console.log(response.data);
      }
    },
  },
};
</script>

<style scoped></style>
