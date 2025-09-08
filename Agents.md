# Agent Development Guidelines

Welcome, Agent! As we collaborate on this project, please follow these guidelines to ensure a smooth, maintainable, and effective development process.

---

## 1. Pair Programming Mindset

-   **You are not working alone.** Always communicate your understanding, ask clarifying questions, and confirm your approach before starting any implementation.
-   **Discuss before you code:** If you are unsure about a requirement, feature, or implementation detail, ask for clarification.

---

## 2. Problem Understanding

-   **Never start coding without fully understanding the problem.**
-   **Restate the problem** in your own words and confirm with your pair (me) before proceeding.

---

## 3. Confirmation Before Action

-   **Before running any command or making significant changes, confirm with your pair.**
-   **Example:**
    > "I am about to run `php artisan migrate:fresh`. Is that okay?"

---

## 4. Coding Standards

-   **Follow OOP (Object-Oriented Programming) principles.**
-   **Use strict typing** wherever possible (e.g., PHP 8+ type hints, return types).
-   **Keep code simple and readable.** Avoid over-engineering.

---

## 5. Modular Feature Development

-   **Each feature should be modular.**
-   For example, if building a customer-related feature:
    -   Create a module under the `app/` directory (e.g., `app/Customer/`).
    -   Place all related controllers, services, requests, and resources in this module.
    -   **Exceptions:** Models, migrations, seeders, factories, and config files should remain in their default Laravel locations.

---

## 6. Frontend Integration

-   **This project uses [Livewire](https://laravel-livewire.com/) for the frontend.**
-   When building features, ensure backend APIs, controllers, and components are compatible with Livewire.
-   **Coordinate with Livewire components** for any UI-related changes.

---

## 7. Communication

-   **If you need more information, always ask.**
-   **Never assume.** Clarify requirements, edge cases, and expected behaviors.

---

## 8. Audit Trails & Activity Logs

-   **Add audit trails and activity logs wherever needed.**
-   For any action that changes data (create, update, delete), ensure the action is logged.
-   Use a centralized logging/audit system if available, or discuss the best approach with your pair.

---

## 9. General Best Practices

-   **Write tests** for new features and bug fixes.
-   **Document your code** and decisions.
-   **Keep pull requests small and focused.**
-   **Review code with your pair before merging.**

---

## Example Workflow

1. **Understand the task:**

    - Ask clarifying questions.
    - Restate the problem.

2. **Plan the implementation:**

    - Propose a modular structure.
    - Confirm with your pair.

3. **Implement with OOP and strict typing.**

4. **Integrate with Livewire as needed.**

5. **Add audit trails/activity logs.**

6. **Test and document.**

7. **Review together before merging.**

---

Let's build great software, together!
