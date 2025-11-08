<template>
  <span
    :class="badgeClasses"
    class="px-2 py-1 rounded text-sm font-semibold"
  >
    {{ text }}
  </span>
</template>

<script>
export default {
  name: 'SampleBadge',
  props: {
    text: {
      type: String,
      required: true,
      default: 'Badge'
    },
    color: {
      type: String,
      default: 'blue' // tailwind color name: blue, red, green, purple, etc.
    }
  },
  computed: {
    badgeClasses() {
      // Tailwind dynamic classes based on color prop
      return `bg-${this.color}-100 text-${this.color}-800`;
    }
  }
};
</script>

<style scoped>
/* optional: smooth transition on hover */
span {
  transition: all 0.2s ease-in-out;
}
</style>
