<script setup lang="ts">
import { countries } from 'country-data-list';
import * as Flags from 'country-flag-icons/string/3x2';
import { ChevronDown, ChevronUp } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Command, CommandEmpty, CommandInput, CommandItem, CommandList } from '@/components/ui/command';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import CommandGroup from './ui/command/CommandGroup.vue';

const props = defineProps({
    attribute: {
        type: String,
        required: false,
    }
});

const countryCode = defineModel<string>('countryCode', { required: true });

const open = ref(false);

const selectedCountry = computed(() => {
    return countries.all.find((country) =>
        country.countryCallingCodes[0] === countryCode.value ||
        country.name === countryCode.value ||
        country.alpha2 === countryCode.value
    );
});

const handleSelect = (country: any) => {
    countryCode.value = props.attribute === 'code' ? country.countryCallingCodes[0] : country.name;
    open.value = false;
};

</script>
<template>
    <Popover v-model:open="open">
        <PopoverTrigger as-child>
            <Button variant="outline" role="combobox" :aria-expanded="open" class="w-full">
                <div v-if="selectedCountry" class="flex items-center gap-2">
                    <span v-html="Flags[selectedCountry.alpha2 as keyof typeof Flags]" :alt="selectedCountry.name"
                        class="w-6"></span>
                    <span>{{ attribute === 'code' ? selectedCountry.countryCallingCodes[0] : selectedCountry.name
                        }}</span>
                </div>
                <div v-else>
                    Select Country
                </div>
                <ChevronUp v-if="open" class="size-4" />
                <ChevronDown v-else class="size-4" />
            </Button>
        </PopoverTrigger>
        <PopoverContent class="w-[250px] p-0">
            <Command>
                <CommandInput placeholder="Search..." />
                <CommandList>
                    <CommandEmpty>No results.</CommandEmpty>
                    <CommandGroup>
                        <CommandItem
                            v-for="country in countries.all.filter((country) => country.countryCallingCodes.length > 0)"
                            :key="country.alpha2" :value="country.name" @select="() => handleSelect(country)">
                            <div class="flex items-center gap-2">
                                <span v-html="Flags[country.alpha2 as keyof typeof Flags]" :alt="country.name"
                                    class="w-6"></span>
                                <span>{{ country.name }} ({{ country.countryCallingCodes[0] }})
                                </span>
                            </div>
                        </CommandItem>
                    </CommandGroup>
                </CommandList>
            </Command>
        </PopoverContent>
    </Popover>
</template>
