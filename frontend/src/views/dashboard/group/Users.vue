<template>
  <div>
    <a-modal
      title="Add user"
      :visible="addUserModalIsVisible"
      :footer="null"
      @cancel="handleCancelAddUserModal"
    >
      <a-table
        :columns="addUserTableColumns"
        :data-source="notExistsInGroupUsers"
        :pagination="true"
      >
        <template slot="login" slot-scope="login">
          <div class="table-info">
            <h6 class="m-0">{{ login }}</h6>
          </div>
        </template>
        <template slot="role" slot-scope="role">
          <div class="author-info">
            <h6 class="m-0">{{ role }}</h6>
          </div>
        </template>
        <template slot="action" slot-scope="text, record">
          <a @click="() => handleAddUserInGroup(record)" href="javascript:;"
            >Add</a
          >
        </template>
      </a-table>
    </a-modal>
    <a-row type="flex" justify="end">
      <a-button type="primary" @click="handleAddUser"> Add user</a-button>
    </a-row>
    <a-row :gutter="24" type="flex">
      <a-col :span="24" class="mb-24">
        <a-card
          :bordered="false"
          class="header-solid h-full"
          :bodyStyle="{ padding: 0 }"
        >
          <a-table :columns="columns" :data-source="users" :pagination="true">
            <template slot="login" slot-scope="login">
              <div class="table-info">
                <h6 class="m-0">{{ login }}</h6>
              </div>
            </template>
            <template slot="role" slot-scope="role">
              <div class="author-info">
                <h6 class="m-0">{{ role }}</h6>
              </div>
            </template>
            <template slot="action" slot-scope="text, record">
              <a
                @click="() => handleRemoveUserFromGroup(record)"
                href="javascript:;"
                >Remove</a
              >
            </template>
          </a-table>
        </a-card>
      </a-col>
    </a-row>
  </div>
</template>

<script>
import { groupsAPI } from "@/services/http/api/groups";
import CreateSensorForm from "@/components/Forms/Sensors/CreateSensorForm.vue";
import { usersApi } from "@/services/http/api/users";
import { notification } from "ant-design-vue";

export default {
  name: "Users",
  components: { CreateSensorForm },
  data() {
    return {
      users: [],
      notExistsInGroupUsers: [],
      columns: [
        {
          title: "Login",
          dataIndex: "login",
          scopedSlots: { customRender: "login" },
          width: 1000,
        },
        {
          title: "Role",
          dataIndex: "role",
          scopedSlots: { customRender: "role" },
        },
        {
          title: "Action",
          key: "action",
          scopedSlots: { customRender: "action" },
        },
      ],
      addUserTableColumns: [
        {
          title: "Login",
          dataIndex: "login",
          scopedSlots: { customRender: "login" },
        },
        {
          title: "Role",
          dataIndex: "role",
          scopedSlots: { customRender: "role" },
        },
        {
          title: "Action",
          key: "action",
          scopedSlots: { customRender: "action" },
        },
      ],
      addUserModalIsVisible: false,
    };
  },
  created() {
    this.getUsers();
  },
  methods: {
    async getUsers() {
      const [error, response] = await groupsAPI.getAllUsers(
        this.$route.params.id
      );

      if (error) {
        console.log(error);
      } else {
        this.users = response.data.map((user) => ({
          id: user.id,
          ...user.attributes,
        }));
      }
    },
    async getNotExistsInGroupUsers() {
      const [error, data] = await usersApi.getAll();

      if (error) {
        console.log(error);
      } else {
        const allUsers = data.data.map((user) => {
          return {
            id: user.id,
            ...user.attributes,
          };
        });

        this.notExistsInGroupUsers = allUsers.filter((user) => {
          const isExistsInGroupUser =
            this.users.filter(
              (existsInGroupUser) => existsInGroupUser.id === user.id
            ).length > 0;

          return !isExistsInGroupUser;
        });

        console.log(this.notExistsInGroupUsers);
      }
    },
    handleCancelAddUserModal() {
      this.addUserModalIsVisible = false;
    },
    handleAddUser() {
      this.addUserModalIsVisible = true;
    },
    async handleAddUserInGroup(record) {
      const [error, response] = await groupsAPI.addUser(
        this.$route.params.id,
        record.id
      );

      if (error) {
        console.log(error);
      } else {
        notification.success({
          message: `User added !`,
          description: "Success",
          duration: 3,
        });
        await this.getUsers();
        await this.getNotExistsInGroupUsers();
      }
    },
    async handleRemoveUserFromGroup(record) {
      const [error, response] = await groupsAPI.removeUser(
        this.$route.params.id,
        record.id
      );

      if (error) {
        console.log(error);
      } else {
        notification.success({
          message: `User removed !`,
          description: "Success",
          duration: 3,
        });
        await this.getUsers();
        await this.getNotExistsInGroupUsers();
      }
    },
  },
  watch: {
    addUserModalIsVisible: {
      handler(value) {
        if (value) {
          this.getNotExistsInGroupUsers();
        }
      },
    },
  },
};
</script>

<style scoped></style>
