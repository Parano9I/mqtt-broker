<template>
  <div style="padding: 2px">
    <a-modal
      title="Create group"
      :visible="createGroupModalIsVisible"
      :footer="null"
      @cancel="handleCancelCreateGroupModal"
    >
      <create-group-form
        v-if="createGroupModalIsVisible"
        @onAfterSubmit="handleAfterCreateGroup"
      />
    </a-modal>
    <a-row type="flex" justify="end">
      <a-button type="primary" @click="handleCreateGroup">
        Create Group
      </a-button>
    </a-row>
    <a-row :gutter="24" type="flex">
      <a-col :span="24" class="mb-24">
        <a-card
          :bordered="false"
          class="header-solid h-full"
          :bodyStyle="{ padding: 0 }"
        >
          <a-table
            :columns="columns"
            :data-source="groups"
            :pagination="true"
            row-key="id"
          >
            <template slot="group-title" slot-scope="title, record">
              <div class="table-info">
                <router-link :to="`/dashboard/groups/${record.id}`">
                  {{ record.title }}
                </router-link>
              </div>
            </template>
            <template slot="action" slot-scope="text, record">
              <a-popconfirm
                v-if="groups.length"
                title="Sure to delete?"
                @confirm="() => handleGroupDelete(record.id)"
              >
                <a href="javascript:;">Delete</a>
              </a-popconfirm>
            </template>
          </a-table>
        </a-card>
      </a-col>
    </a-row>
  </div>
</template>

<script>
import { groupsAPI } from "@/services/http/api/groups";
import CreateGroupForm from "@/components/Forms/Groups/CreateGroupForm.vue";
import { notification } from "ant-design-vue";

export default {
  name: "Groups",
  components: { CreateGroupForm },
  data() {
    return {
      groups: [],
      columns: [
        {
          title: "Title",
          dataIndex: "title",
          scopedSlots: { customRender: "group-title" },
        },
        {
          title: "Description",
          dataIndex: "description",
        },
        {
          title: "Users",
          dataIndex: "users_count",
        },
        {
          title: "Action",
          key: "action",
          scopedSlots: { customRender: "action" },
        },
      ],
      createGroupModalIsVisible: false,
    };
  },
  mounted() {
    this.getGroups();
  },
  methods: {
    async getGroups() {
      const [error, data] = await groupsAPI.getAll();

      if (error) {
        console.log(error);
      } else {
        this.groups = data.data.map((group) => ({
          id: group.id,
          ...group.attributes,
        }));
      }
    },
    handleCreateGroup() {
      this.createGroupModalIsVisible = true;
    },
    handleCancelCreateGroupModal() {
      this.createGroupModalIsVisible = false;
    },
    handleAfterCreateGroup(response) {
      this.getGroups();
      notification.success({
        message: `Create group success !`,
        description: "Success",
        duration: 3,
      });
      this.createGroupModalIsVisible = false;
    },
    async handleGroupDelete(id) {
      const [error, response] = await groupsAPI.delete(id);

      if (error) {
        notification.error({
          message: "Error !",
          description: error.message,
          duration: 3,
        });
      } else {
        notification.success({
          message: `Delete group success !`,
          description: "Success",
          duration: 3,
        });
        await this.getGroups();
      }
    },
  },
};
</script>

<style scoped></style>
