import React, { useRef } from 'react'
import { useTranslate } from '@refinedev/core'
import { PrimaryTableCol, Input } from 'tdesign-react/esm'
import {
  PageTable,
  FilterItem,
  MediaText,
  TableRef,
  EditLink,
  DeleteLink,
  CreateButton,
} from '@duxweb/dux-refine'

const List = () => {
  const translate = useTranslate()

  const columns = React.useMemo<PrimaryTableCol[]>(
    () => [
      { colKey: 'row-select', type: 'multiple' },
      {
        colKey: 'id',
        sorter: true,
        sortType: 'all',
        title: 'ID',
        width: 100,
      },
      {
        colKey: 'title',
        title: translate('poster.design.fields.title'),
        minWidth: 200,
        cell: ({ row }) => {
          return (
            <MediaText size='small'>
              {row.images?.[0] && <MediaText.Image src={row.images}></MediaText.Image>}
              <MediaText.Title>{row.title}</MediaText.Title>
              <MediaText.Desc>{row.subtitle}</MediaText.Desc>
            </MediaText>
          )
        },
      },
      {
        colKey: 'link',
        title: translate('table.actions'),
        fixed: 'right',
        align: 'center',
        width: 120,
        cell: ({ row }) => {
          return (
            <div className='flex justify-center gap-4'>
              <EditLink rowId={row.id} />
              <DeleteLink rowId={row.id} />
            </div>
          )
        },
      },
    ],
    // eslint-disable-next-line react-hooks/exhaustive-deps
    [translate],
  )

  const table = useRef<TableRef>(null)

  return (
    <PageTable
      ref={table}
      columns={columns}
      actionRender={() => <CreateButton />}
      filterRender={() => {
        return (
          <>
            <FilterItem name='keyword'>
              <Input placeholder={translate('poster.design.validate.title')} />
            </FilterItem>
          </>
        )
      }}
    />
  )
}

export default List
