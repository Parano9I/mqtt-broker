<template>
  <div>
    <a-modal
      title="Create group"
      :visible="createSensorModalIsVisible"
      :footer="null"
      @cancel="handleCancelCreateSensorModal"
    >
      <create-sensor-form
        v-if="createSensorModalIsVisible"
        @onAfterSubmit="handleAfterCreateSensor"
      />
    </a-modal>
    <a-row type="flex" justify="end">
      <a-button type="primary" @click="handleCreateSensor">
        Create Sensor
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
            :data-source="sensors"
            :pagination="true"
            row-key="id"
          >
            <template slot="secret" slot-scope="text, record">
              <a-button @click="() => handleCopySecret(record.secret)"
                >Copy secret</a-button
              >
            </template>
            <template slot="status" slot-scope="status">
              <p :style="{ color: status === 'offline' ? 'red' : 'green' }">
                {{ status }}
              </p>
            </template>
            <template slot="action" slot-scope="text, record">
              <a-popconfirm
                v-if="sensors.length"
                title="Sure to delete?"
                @confirm="() => handleSensorDelete(record)"
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
import { useGroupStore } from "@/store/group";
import CreateSensorForm from "@/components/Forms/Sensors/CreateSensorForm.vue";
import { notification } from "ant-design-vue";
import { sensorsAPI } from "@/services/http/api/sensors";

export default {
  name: "Home",
  components: { CreateSensorForm },
  data() {
    return {
      groupState: useGroupStore(),
      group: {},
      sensors: [],
      createSensorModalIsVisible: false,
      columns: [
        {
          title: "Id",
          dataIndex: "id",
        },
        {
          title: "Name",
          dataIndex: "name",
        },
        {
          title: "Path",
          dataIndex: "path",
        },
        {
          title: "Status",
          dataIndex: "status",
          scopedSlots: { customRender: "status" },
        },
        {
          title: "Secret",
          key: "secret",
          scopedSlots: { customRender: "secret" },
        },
        {
          title: "Action",
          key: "action",
          scopedSlots: { customRender: "action" },
        },
      ],
    };
  },
  async created() {
    await this.getGroup();

    this.group = {
      ...this.group,
      ...this.groupState.$state,
    };

    await this.getSensors();
  },
  methods: {
    async getGroup() {
      const [error, response] = await groupsAPI.show(this.$route.params?.id);

      if (error) {
        console.log(error);
      } else {
        const data = response.data;
        this.groupState.setGroup({
          id: data.id,
          ...data.attributes,
        });
      }
    },
    async getSensors() {
      const [error, response] = await groupsAPI.getAllSensors(
        this.$route.params?.id
      );

      if (error) {
        console.log(error);
      } else {
        this.sensors = response.data.map((sensor) => ({
          id: sensor.id,
          ...sensor.attributes,
        }));
      }
    },
    handleCopySecret(secret) {
      navigator.clipboard.writeText(secret).then(() => {
        notification.success({
          message: `Secret copied !`,
          description: "Success",
          duration: 2,
        });
      });
    },
    async handleSensorDelete(record) {
      const params = {
        group: this.$route.params.id,
        topic: record.topic,
        sensor: record.id,
      };

      const [error, response] = await sensorsAPI.delete(params);

      if (error) {
        notification.error({
          message: "Error !",
          description: error.message,
          duration: 3,
        });
      } else {
        notification.success({
          message: `Delete sensor success !`,
          description: "Success",
          duration: 3,
        });
        await this.getSensors();
      }
    },
    handleCancelCreateSensorModal() {
      this.createSensorModalIsVisible = false;
    },
    handleAfterCreateSensor() {
      this.getSensors();
      notification.success({
        message: `Create group success !`,
        description: "Success",
        duration: 3,
      });
      this.createSensorModalIsVisible = false;
    },
    handleCreateSensor() {
      this.createSensorModalIsVisible = true;
    },
  },
};
</script>

<style scoped></style>
