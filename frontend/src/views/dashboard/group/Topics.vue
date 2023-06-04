<template>
  <div>
    <a-modal
      title="Create group"
      :visible="createTopicModalIsVisible"
      :footer="null"
      @cancel="handleCancelCreateTopicModal"
    >
      <create-topic-form
        v-if="createTopicModalIsVisible"
        @onAfterSubmit="handleAfterCreateTopic"
      />
    </a-modal>
    <a-row type="flex" justify="end">
      <a-button type="primary" @click="handleCreateTopic">
        Create topic
      </a-button>
    </a-row>
    <a-space v-if="selectedTopicName" :size="30" align="center">
      <span>
        You selected topic: <b>{{ selectedTopicName }}</b>
      </span>
      <a-button type="danger" @click="handleRemoveTopic">
        Remove topic
      </a-button>
    </a-space>
    <a-tree
      :tree-data="topics"
      :replaceFields="treeFields"
      default-expand-all
      @select="onSelectTopic"
    />
  </div>
</template>

<script>
import { topicsAPI } from "@/services/http/api/topics";
import CreateTopicForm from "@/components/Forms/Topics/CreateTopicForm.vue";
import { notification } from "ant-design-vue";

export default {
  name: "Topics",
  components: { CreateTopicForm },
  data() {
    return {
      topics: [],
      treeFields: {
        title: "name",
        key: "id",
        value: "id",
        pId: "parent_id",
      },
      createTopicModalIsVisible: false,
      selectedTopic: null,
    };
  },
  created() {
    this.getTopics();
  },
  methods: {
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
    handleCancelCreateTopicModal() {
      this.createTopicModalIsVisible = false;
    },
    handleAfterCreateTopic() {
      this.getTopics();
      notification.success({
        message: `Create topic success !`,
        description: "Success",
        duration: 3,
      });
      this.createTopicModalIsVisible = false;
    },
    handleCreateTopic() {
      this.createTopicModalIsVisible = true;
    },
    async handleRemoveTopic() {
      const params = {
        group: this.$route.params.id,
        topic: this.selectedTopic.id,
      };

      const [error, response] = await topicsAPI.delete(params);

      if (error) {
        notification.error({
          message: "Error !",
          description: error.message,
          duration: 3,
        });
      } else {
        notification.success({
          message: `Delete topic success !`,
          description: "Success",
          duration: 3,
        });
        await this.getTopics();
        this.selectedTopic = null;
      }
    },
    onSelectTopic(selectedKeys, info) {
      this.selectedTopic = info.selectedNodes[0].data.props;
    },
  },
  computed: {
    selectedTopicName() {
      return this.selectedTopic?.name ?? "";
    },
  },
};
</script>

<style scoped></style>
