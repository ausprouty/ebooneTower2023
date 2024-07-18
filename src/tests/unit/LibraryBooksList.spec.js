import { mount } from '@vue/test-utils'
import LibraryBookList from '@/components/LibraryBookList.vue'

describe('LibraryBookList.vue', () => {
  const libraryBooks = [
    {
      /* book data */
    },
    {
      /* book data */
    },
    // Add more book data as needed for your tests
  ]

  it('renders book components correctly', () => {
    const wrapper = mount(LibraryBookList, {
      props: { libraryBooks },
    })

    expect(wrapper.findAllComponents({ name: 'LibraryBookTitle' }).length).toBe(
      libraryBooks.length
    )
    expect(wrapper.findAllComponents({ name: 'LibraryBookCode' }).length).toBe(
      libraryBooks.length
    )
    expect(wrapper.findAllComponents({ name: 'LibraryBookImage' }).length).toBe(
      libraryBooks.length
    )
    expect(
      wrapper.findAllComponents({ name: 'LibraryBookFormat' }).length
    ).toBe(libraryBooks.length)
    expect(wrapper.findAllComponents({ name: 'LibraryBookStyle' }).length).toBe(
      libraryBooks.length
    )
    expect(
      wrapper.findAllComponents({ name: 'LibraryBookTemplate' }).length
    ).toBe(libraryBooks.length)
    expect(
      wrapper.findAllComponents({ name: 'LibraryBookPermission' }).length
    ).toBe(libraryBooks.length)
  })

  // Add more tests as needed
})
