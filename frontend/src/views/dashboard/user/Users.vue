<template>
  <div>
    <a-row :gutter="24" type="flex">
      <a-col :span="24" class="mb-24">
        <a-card
          :bordered="false"
          class="header-solid h-full"
          :bodyStyle="{ padding: 0 }"
        >
          <template #title>
            <a-row type="flex" align="middle">
              <a-col :span="24" :md="12">
                <h5 class="font-semibold m-0">Users</h5>
              </a-col>
            </a-row>
          </template>
          <a-table :columns="columns" :data-source="users" :pagination="true">
            <template slot="login" slot-scope="login">
              <div class="table-info">
                <h6 class="m-0">{{ login }}</h6>
              </div>
            </template>
            <template slot="email" slot-scope="email">
              <div class="author-info">
                <h6 class="m-0">{{ email }}</h6>
              </div>
            </template>
            <template slot="role" slot-scope="role">
              <div class="author-info">
                <h6 class="m-0">{{ role }}</h6>
              </div>
            </template>
          </a-table>
        </a-card>
      </a-col>
    </a-row>
  </div>
</template>

<script>
import { usersApi } from "@/services/http/api/users";

export default {
  name: "Users",
  data() {
    return {
      users: [],
      columns: [
        {
          title: "Login",
          dataIndex: "login",
          scopedSlots: { customRender: "login" },
        },
        {
          title: "Email",
          dataIndex: "email",
          scopedSlots: { customRender: "email" },
        },
        {
          title: "Role",
          dataIndex: "role",
          scopedSlots: { customRender: "role" },
        },
      ],
    };
  },
  mounted() {
    this.getUsers();
  },
  methods: {
    async getUsers() {
      const [error, data] = await usersApi.getAll();

      if (error) {
        console.log(error);
      } else {
        this.users = data.data.map((user) => ({
          id: user.id,
          ...user.attributes,
        }));
      }
    },
  },
};
</script>

<style scoped></style>
