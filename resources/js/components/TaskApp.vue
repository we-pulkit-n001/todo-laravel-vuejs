<template>
  <div class="container mt-4">
    <h1 class="mb-4 text-center">Zestlist App</h1>
    <div class="mb-4">
      <h2>{{ isEditing ? 'Edit Task' : 'Create Task' }}</h2>
      <form @submit.prevent="submitTodo">
        <div class="mb-3">
          <label for="todoName" class="form-label">Task Name</label>
          <input 
            id="todoName" 
            v-model="newTask.name" 
            type="text" 
            class="form-control" 
            placeholder="Enter Task Name" 
            required 
          />
        </div>
        <div class="mb-3">
          <label for="todoDescription" class="form-label">Description</label>
          <input 
            id="todoDescription" 
            v-model="newTask.description" 
            type="text" 
            class="form-control" 
            placeholder="Enter Task Description" 
          />
        </div>
        <button type="submit" class="btn btn-primary">
          {{ isEditing ? 'Update' : 'Add' }}
        </button>
      </form>
    </div>
    <div class="mb-4">
      <ul class="nav nav-tabs">
        <li class="nav-item">
          <a 
            class="nav-link" 
            :class="{ active: currentTab === 'all' }" 
            href="#" 
            @click.prevent="currentTab = 'all'"
          >
            All Tasks
          </a>
        </li>
        <li class="nav-item">
          <a 
            class="nav-link" 
            :class="{ active: currentTab === 'completed' }" 
            href="#" 
            @click.prevent="currentTab = 'completed'"
          >
            Completed Tasks
          </a>
        </li>
      </ul>
      <div class="mt-3">
        <ul class="list-group">
          <template v-if="currentTab === 'all' && noPendingTasks">
            <li class="list-group-item">No tasks are pending!</li>
          </template>
          <template v-else>
            <li v-for="todo in filteredTodos" :key="todo.id" class="list-group-item d-flex justify-content-between align-items-center">
              {{ todo.name }} : {{ todo.description }} 
              <span :class="{'text-success': todo.is_completed, 'text-danger': !todo.is_completed}">
                {{ todo.is_completed ? 'Completed' : 'Incomplete' }}
              </span>
              <div>
                <button 
                  @click="markAsCompleted(todo.id)" 
                  :disabled="todo.is_completed"
                  class="btn btn-complete btn-sm me-2"
                >
                  Mark as Completed
                </button>
                <button 
                  v-if="!todo.is_completed"
                  @click="editTodo(todo)"
                  class="btn btn-edit btn-sm me-2"
                >
                  Edit
                </button>
                <button 
                  @click="confirmDelete(todo.id)"
                  class="btn btn-danger btn-sm"
                >
                  Delete
                </button>
              </div>
            </li>
          </template>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      tasks: [],
      newTask: {
        name: '',
        description: ''
      },
      editTodoData: null,
      currentTab: 'all', 
      isEditing: false 
    };
  },
  computed: {
    filteredTodos() {
      if (this.currentTab === 'completed') {
        return this.tasks.filter(todo => todo.is_completed);
      }
      return this.tasks.filter(todo => !todo.is_completed);
    },
    noPendingTasks() {
      return this.currentTab === 'all' && this.tasks.every(todo => todo.is_completed);
    }
  },
  mounted() {
    this.fetchTodos();
  },
  methods: {
    fetchTodos() {
      axios.get('/api/tasks').then(response => {
        this.tasks = response.data;
      });
    },
    submitTodo() {
      if (this.isEditing && this.newTask.id) {
        axios.put(`/api/tasks/${this.newTask.id}`, this.newTask).then(() => {
          this.fetchTodos();
          this.resetForm();
        });
      } else {
        axios.post('/api/tasks', this.newTask).then(response => {
          this.tasks.push(response.data);
          this.resetForm();
        });
      }
    },
    markAsCompleted(id) {
      axios.patch(`/api/tasks/${id}/complete`).then(() => {
        this.fetchTodos();
      });
    },
    editTodo(todo) {
      this.newTask = { ...todo };
      this.isEditing = true;
    },
    confirmDelete(id) {
      if (window.confirm('Are you sure you want to delete this task?')) {
        this.deleteTodo(id);
      }
    },
    deleteTodo(id) {
      axios.delete(`/api/tasks/${id}`).then(() => {
        this.fetchTodos();
      });
    },
    resetForm() {
      this.newTask = { name: '', description: '' };
      this.isEditing = false;
    }
  }
};
</script>

<style scoped>

.container {
  max-width: 600px;
  margin: auto;
  padding: 20px;
  background: #f8f9fa;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

h1 {
  font-size: 2rem;
  color: #007BFF; 
}

h2 {
  font-size: 1.5rem;
  margin-bottom: 20px;
}

.form-label {
  font-weight: bold;
  color: #495057;
}

.form-control {
  border-radius: 5px;
  border: 1px solid #ced4da;
}

.btn-primary {
  background-color: #007BFF; 
  border: none;
}

.btn-complete {
  background-color: #28a745;
  color: white;
}

.btn-edit {
  background-color: #343a40;
  color: white;
}

.btn-danger {
  background-color: #dc3545; 
  color: white;
}

.nav-tabs .nav-link {
  color: #007BFF; 
}

.nav-tabs .nav-link.active {
  color: #fff;
  background-color: #007BFF;
}

.list-group-item {
  background-color: #ffffff;
  border: 1px solid #dee2e6;
}

.list-group-item:hover {
  background-color: #f1f1f1;
}

</style>
